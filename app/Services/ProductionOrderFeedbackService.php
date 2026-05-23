<?php

namespace App\Services;

use App\Models\ERP\Bin;
use App\Models\ERP\StockEntry;
use App\Models\ERP\WorkOrder;
use App\Models\MES\FeedbackedLog;
use App\Models\MES\JobTicket;
use App\Models\MES\Operation;
use App\Models\MES\ProductionOrder;
use App\Services\Erp\StockEntryApiService;
use App\Services\Erp\StockEntryPayloadBuilder;
use App\Traits\GeneralTrait;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;

class ProductionOrderFeedbackService
{
    use GeneralTrait;

    /** @var StockEntryApiService */
    protected $stockEntryApiService;

    /** @var StockEntryPayloadBuilder */
    protected $payloadBuilder;

    public function __construct(
        StockEntryApiService $stockEntryApiService,
        StockEntryPayloadBuilder $payloadBuilder
    ) {
        $this->stockEntryApiService = $stockEntryApiService;
        $this->payloadBuilder = $payloadBuilder;
    }

    /**
     * @param string $productionOrder
     * @param array $data must include fg_completed_qty
     * @return array{success: int, message: string, stock_entry?: string}
     */
    public function create($productionOrder, array $data)
    {
        $this->validateSession();

        $fgCompletedQty = (float) $data['fg_completed_qty'];

        $this->validateProductionOrder($productionOrder);

        $workOrder = WorkOrder::find($productionOrder);
        if (! $workOrder) {
            throw new Exception('Production order not found.');
        }

        $mesProductionOrder = ProductionOrder::findByProductionOrder($productionOrder);
        if (! $mesProductionOrder) {
            throw new Exception('MES production order not found.');
        }

        $remarksOverride = $this->validateFeedbackQty(
            $workOrder,
            $mesProductionOrder,
            $fgCompletedQty
        );

        $erpProducedQtyBeforeFeedback = $workOrder->produced_qty;

        $docstatus = $this->resolveDocStatus($mesProductionOrder->fg_warehouse);

        $built = $this->payloadBuilder->build(
            $workOrder,
            $mesProductionOrder,
            $fgCompletedQty,
            $docstatus
        );

        DB::connection('mysql_mes')->beginTransaction();

        try {
            $stockEntryName = $this->stockEntryApiService->createManufactureEntry(
                $built['payload'],
                $docstatus
            );

            if ((int) $docstatus === 1) {
                $this->verifyBinQuantities(
                    $workOrder,
                    $mesProductionOrder,
                    $built['item_codes'],
                    $built['rm_bin_expectations'],
                    $built['fg_bin_expectations']
                );

                $this->assertStockEntrySubmitted($stockEntryName);

                $this->syncMesAfterFeedback(
                    $workOrder,
                    $mesProductionOrder,
                    $stockEntryName,
                    $fgCompletedQty,
                    $remarksOverride,
                    $erpProducedQtyBeforeFeedback
                );
            }

            DB::connection('mysql_mes')->commit();

            return [
                'success' => 1,
                'message' => 'Stock Entry has been created.',
                'stock_entry' => $stockEntryName,
            ];
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollBack();
            throw $e;
        }
    }

    protected function validateSession()
    {
        if (! Auth::user()) {
            throw new Exception('Session Expired. Please login to continue.');
        }
    }

    protected function validateProductionOrder($productionOrder)
    {
        if (! JobTicket::forProductionOrder($productionOrder)->exists()) {
            throw new Exception(
                '<center>Cannot create feedback. <br> Production order has no workstation / process.</center>'
            );
        }

        $hasTransfer = StockEntry::forWorkOrder($productionOrder)
            ->materialTransferForManufacture()
            ->submitted()
            ->exists();

        if (! $hasTransfer) {
            throw new Exception('Materials unavailable.');
        }
    }

    /**
     * @return string|null remarks override value
     */
    protected function validateFeedbackQty(WorkOrder $workOrder, ProductionOrder $mesProductionOrder, $fgCompletedQty)
    {
        if ($fgCompletedQty <= 0) {
            throw new Exception('Feedback qty cannot be equal to 0.');
        }

        $producedQty = $workOrder->produced_qty + $fgCompletedQty;

        if ($producedQty >= (int) $workOrder->qty && $workOrder->material_transferred_for_manufacturing > 0) {
            $pendingMtfm = StockEntry::firstPendingMaterialTransferLine($workOrder->name);

            if ($pendingMtfm) {
                throw new Exception(
                    '<center>There are pending material request for issue. <br><br> Insufficient stock for '
                    . $pendingMtfm->item_code . ' in ' . $pendingMtfm->t_warehouse . '.</center>'
                );
            }
        }

        $operationDetails = Operation::find($mesProductionOrder->operation_id);

        if ($operationDetails && $operationDetails->isAssembly()) {
            $totalFeedbackQty = $mesProductionOrder->feedback_qty + $fgCompletedQty;
            if ($totalFeedbackQty > $mesProductionOrder->qty_to_manufacture) {
                throw new Exception(
                    '<center>Feedback Qty should not be greater than <b>'
                    . $mesProductionOrder->qty_to_manufacture . '</b>.'
                );
            }
        }

        $remarksOverride = null;
        if ($producedQty > $mesProductionOrder->produced_qty) {
            $remarksOverride = 'Override';
        }

        return $remarksOverride;
    }

    public function resolveDocStatus($warehouse)
    {
        $draftWarehouses = [
            'P2 - Housing Temporary - FI1',
        ];

        return in_array($warehouse, $draftWarehouses, true) ? 0 : 1;
    }

    public function shouldRedirectToBundleFeedback($mesProductionOrder)
    {
        return $mesProductionOrder && (int) $mesProductionOrder->is_stock_item < 1;
    }

    /**
     * @param string $stockEntryName
     * @return StockEntry|array
     */
    protected function assertStockEntrySubmitted($stockEntryName)
    {
        $submitted = StockEntry::where('name', $stockEntryName)
            ->manufacture()
            ->submitted()
            ->first();

        if ($submitted) {
            return $submitted;
        }

        $doc = $this->stockEntryApiService->get($stockEntryName);

        if (isset($doc['docstatus']) && (int) $doc['docstatus'] === 1) {
            return $doc;
        }

        throw new Exception('There was a problem create stock entry. Please try again.');
    }

    protected function verifyBinQuantities(
        WorkOrder $workOrder,
        ProductionOrder $mesProductionOrder,
        array $itemCodes,
        array $rmBinExpectations,
        array $fgBinExpectations
    ) {
        $rawMaterialsCurrentBin = Bin::forItemsInWarehouse($itemCodes, $workOrder->wip_warehouse)
            ->pluck('actual_qty', 'item_code')
            ->toArray();

        foreach ($rawMaterialsCurrentBin as $rmItemCode => $rmQty) {
            $expected = isset($rmBinExpectations[$rmItemCode]['expected_qty_after_transaction'])
                ? $rmBinExpectations[$rmItemCode]['expected_qty_after_transaction']
                : null;

            if ($expected === null) {
                throw new Exception('There was a problem creating feedback. Please reload the page and try again.');
            }

            if (number_format((float) $expected, 4, '.', '') != number_format((float) $rmQty, 4, '.', '')) {
                throw new Exception('There was a problem creating feedback. Please reload the page and try again.');
            }
        }

        $fgCurrentBin = Bin::forItem($workOrder->production_item)
            ->inWarehouse($mesProductionOrder->fg_warehouse)
            ->pluck('actual_qty', 'item_code')
            ->toArray();

        foreach ($fgCurrentBin as $fgItemCode => $fgQty) {
            $expected = isset($fgBinExpectations[$fgItemCode]['expected_qty_after_transaction'])
                ? $fgBinExpectations[$fgItemCode]['expected_qty_after_transaction']
                : null;

            if ($expected === null) {
                throw new Exception('There was a problem creating feedback. Please reload the page and try again.');
            }

            if (number_format((float) $expected, 4, '.', '') != number_format((float) $fgQty, 4, '.', '')) {
                throw new Exception('There was a problem creating feedback. Please reload the page and try again.');
            }
        }
    }

    protected function syncMesAfterFeedback(
        WorkOrder $workOrder,
        ProductionOrder $mesProductionOrder,
        $stockEntryName,
        $fgCompletedQty,
        $remarksOverride,
        $erpProducedQtyBeforeFeedback
    ) {
        $now = Carbon::now();
        $manufacturedQty = $erpProducedQtyBeforeFeedback + $fgCompletedQty;

        $productionDataMes = [
            'last_modified_at' => $now->toDateTimeString(),
            'last_modified_by' => Auth::user()->email,
            'feedback_qty' => $manufacturedQty,
            'status' => $manufacturedQty >= $workOrder->qty ? 'Feedbacked' : 'Partially Feedbacked',
            'remarks' => $remarksOverride,
        ];

        if ($remarksOverride === 'Override') {
            $productionDataMes['produced_qty'] = $manufacturedQty;

            JobTicket::forProductionOrder($workOrder->name)
                ->notCompleted()
                ->update([
                    'completed_qty' => $manufacturedQty,
                    'remarks' => $remarksOverride,
                    'status' => JobTicket::STATUS_COMPLETED,
                    'last_modified_by' => Auth::user()->email,
                ]);
        }

        $mesProductionOrder->update($productionDataMes);

        $feedbackLog = FeedbackedLog::create([
            'production_order' => $mesProductionOrder->production_order,
            'ste_no' => $stockEntryName,
            'item_code' => $workOrder->production_item,
            'item_name' => $workOrder->item_name,
            'feedbacked_qty' => $fgCompletedQty,
            'from_warehouse' => $workOrder->wip_warehouse,
            'to_warehouse' => $mesProductionOrder->fg_warehouse,
            'transaction_date' => $now->format('Y-m-d'),
            'transaction_time' => $now->format('G:i:s'),
            'created_at' => $now->toDateTimeString(),
            'created_by' => Auth::user()->email,
        ]);

        if (! $feedbackLog || ! $feedbackLog->id) {
            throw new Exception('There was a problem creating feedback. Please reload the page and try again.');
        }

        $this->insert_production_scrap($workOrder->name, $fgCompletedQty);
    }
}
