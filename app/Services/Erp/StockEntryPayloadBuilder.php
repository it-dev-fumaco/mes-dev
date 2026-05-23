<?php

namespace App\Services\Erp;

use App\Models\ERP\AthenaTransaction;
use App\Models\ERP\Bin;
use App\Models\ERP\BomItem;
use App\Models\ERP\StockEntryDetail;
use App\Models\ERP\StockReservation;
use App\Models\ERP\Uom;
use App\Models\ERP\WorkOrder;
use App\Models\MES\ProductionOrder;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Exception;

class StockEntryPayloadBuilder
{
    use GeneralTrait;

    const DOCTYPE_STOCK_ENTRY = 'Stock Entry';
    const DOCTYPE_STOCK_ENTRY_DETAIL = 'Stock Entry Detail';
    const EXPENSE_ACCOUNT = 'Cost of Goods Sold - FI';
    const COST_CENTER = 'Main - FI';
    const COMPANY = 'FUMACO Inc.';

    /**
     * @param WorkOrder $workOrder
     * @param ProductionOrder $mesProductionOrder
     * @param float $fgCompletedQty
     * @param int $docstatus
     * @return array
     */
    public function build(WorkOrder $workOrder, ProductionOrder $mesProductionOrder, $fgCompletedQty, $docstatus)
    {
        $productionOrder = $workOrder->name;
        $now = Carbon::now();

        $productionOrderItems = $this->feedback_production_order_items(
            $productionOrder,
            $mesProductionOrder->qty_to_manufacture,
            $fgCompletedQty
        );

        if (count($productionOrderItems) < 1) {
            throw new Exception('Materials unavailable.');
        }

        $itemCodes = array_column($productionOrderItems, 'item_code');

        $rawMaterialsCurrentBin = Bin::forItemsInWarehouse($itemCodes, $workOrder->wip_warehouse)
            ->pluck('actual_qty', 'item_code')
            ->toArray();

        $fgCurrentBin = Bin::forItem($workOrder->production_item)
            ->inWarehouse($mesProductionOrder->fg_warehouse)
            ->pluck('actual_qty', 'item_code')
            ->toArray();

        $stockReservation = StockReservation::totalsGroupedByItem($itemCodes, $workOrder->wip_warehouse)->toArray();
        $steTotalIssued = StockEntryDetail::issuedTotalsGroupedByItem($itemCodes, $workOrder->wip_warehouse)->toArray();
        $atTotalIssued = AthenaTransaction::issuedTotalsGroupedByItem($itemCodes, $workOrder->wip_warehouse)->toArray();

        $apiItems = [];
        $rmBinExpectations = [];
        $rmTotalAmount = 0;
        $idx = 1;

        foreach ($productionOrderItems as $row) {
            $qty = $row['required_qty'];
            $qtyBeforeTransaction = isset($rawMaterialsCurrentBin[$row['item_code']])
                ? $rawMaterialsCurrentBin[$row['item_code']]
                : 0;

            $rmBinExpectations[$row['item_code']]['expected_qty_after_transaction'] = number_format(
                $qtyBeforeTransaction - $qty,
                6,
                '.',
                ''
            );

            $bomMaterial = BomItem::forBom($workOrder->bom_no)
                ->forItem($row['item_code'])
                ->first();

            $valuationRate = 0;
            if (! $bomMaterial) {
                $valuationRate = Bin::forItem($row['item_code'])
                    ->inWarehouse($workOrder->wip_warehouse)
                    ->sum('valuation_rate');
            }

            $baseRate = $bomMaterial ? $bomMaterial->base_rate : $valuationRate;

            if ($qty <= 0) {
                continue;
            }

            $isUomWholeNumber = Uom::find($row['stock_uom']);

            if ($isUomWholeNumber && $isUomWholeNumber->must_be_whole_number == 1) {
                $qty = round($qty);
            }

            $remainingTransferredQty = $row['transferred_qty'] - $row['consumed_qty'];

            if (number_format($remainingTransferredQty, 5, '.', '') < number_format($qty, 5, '.', '')) {
                throw new Exception(
                    'Insufficient transferred qty for ' . $row['item_code'] . ' in ' . $workOrder->wip_warehouse
                );
            }

            if ($qty <= 0) {
                throw new Exception(
                    'Qty cannot be less than or equal to 0 for ' . $row['item_code'] . ' in ' . $workOrder->wip_warehouse
                );
            }

            $actualQty = Bin::forItem($row['item_code'])
                ->inWarehouse($workOrder->wip_warehouse)
                ->sum('actual_qty');

            $reservedQty = 0;
            $consumedReservation = 0;
            if (isset($stockReservation[$row['item_code']])) {
                $reservedQty = $stockReservation[$row['item_code']][0]->total_reserved_qty;
                $consumedReservation = $stockReservation[$row['item_code']][0]->total_consumed_qty;
            }

            $reservedQty = $reservedQty - $consumedReservation;

            $issuedQty = 0;
            if (isset($steTotalIssued[$row['item_code']])) {
                $issuedQty = $steTotalIssued[$row['item_code']][0]->total_issued;
            }

            if (isset($atTotalIssued[$row['item_code']])) {
                $issuedQty += $atTotalIssued[$row['item_code']][0]->total_issued;
            }

            $actualQty = ($actualQty - $issuedQty) - $reservedQty;

            if ((int) $docstatus === 1) {
                $hasProductionOrder = ProductionOrder::forPartHierarchy(
                    $row['item_code'],
                    $mesProductionOrder->parent_item_code,
                    $mesProductionOrder->sales_order,
                    $mesProductionOrder->material_request,
                    $mesProductionOrder->item_code
                )->exists();

                if ($hasProductionOrder) {
                    $insufficientStockMsg = 'Insufficient stock for ' . $row['item_code'] . ' in ' . $workOrder->wip_warehouse . '. One or more production parts are pending for feedback, please check your parts production order.';
                } else {
                    $insufficientStockMsg = 'Insufficient stock for ' . $row['item_code'] . ' in ' . $workOrder->wip_warehouse . '. Some of the components quantity are pending for Issue.';
                }

                if ($qty > $actualQty) {
                    throw new Exception($insufficientStockMsg);
                }
            }

            $basicAmount = $baseRate * $qty;
            $rmTotalAmount += $basicAmount;

            $apiItems[] = $this->buildDetailRow([
                'idx' => $idx,
                'item_code' => $row['item_code'],
                'item_name' => $row['item_name'],
                'description' => $row['description'],
                'qty' => $qty,
                'transfer_qty' => $qty,
                'uom' => $row['stock_uom'],
                'stock_uom' => $row['stock_uom'],
                'conversion_factor' => $bomMaterial ? $bomMaterial->conversion_factor : 1,
                's_warehouse' => $workOrder->wip_warehouse,
                'expense_account' => self::EXPENSE_ACCOUNT,
                'cost_center' => self::COST_CENTER,
                'basic_rate' => $baseRate,
                'valuation_rate' => $baseRate,
                'basic_amount' => $basicAmount,
                'amount' => $basicAmount,
            ]);

            $idx++;
        }

        if (count($apiItems) < 1) {
            throw new Exception('Materials unavailable.');
        }

        $fgRate = $rmTotalAmount / $fgCompletedQty;

        $fgQtyBefore = isset($fgCurrentBin[$workOrder->production_item])
            ? $fgCurrentBin[$workOrder->production_item]
            : 0;

        $fgBinExpectations = [
            $workOrder->production_item => [
                'expected_qty_after_transaction' => number_format(
                    $fgQtyBefore + $fgCompletedQty,
                    6,
                    '.',
                    ''
                ),
            ],
        ];

        $apiItems[] = $this->buildDetailRow([
            'idx' => $idx,
            'item_code' => $workOrder->production_item,
            'item_name' => $workOrder->item_name,
            'description' => $workOrder->description,
            'qty' => $fgCompletedQty,
            'transfer_qty' => $fgCompletedQty,
            'uom' => $workOrder->stock_uom,
            'stock_uom' => $workOrder->stock_uom,
            'conversion_factor' => 1,
            't_warehouse' => $mesProductionOrder->fg_warehouse,
            'expense_account' => self::EXPENSE_ACCOUNT,
            'cost_center' => self::COST_CENTER,
            'basic_rate' => $fgRate,
            'valuation_rate' => $fgRate,
            'basic_amount' => $rmTotalAmount,
            'amount' => $rmTotalAmount,
        ]);

        $payload = $this->filterNullValues([
            'doctype' => self::DOCTYPE_STOCK_ENTRY,
            'naming_series' => 'STE-',
            'stock_entry_type' => 'Manufacture',
            'purpose' => 'Manufacture',
            'company' => self::COMPANY,
            'work_order' => $productionOrder,
            'bom_no' => $workOrder->bom_no,
            'from_bom' => 1,
            'use_multi_level_bom' => 1,
            'fg_completed_qty' => $fgCompletedQty,
            'posting_date' => $now->format('Y-m-d'),
            'posting_time' => $now->format('H:i:s'),
            'set_posting_time' => 0,
            'to_warehouse' => $workOrder->fg_warehouse,
            'project' => $workOrder->project,
            'material_request' => $workOrder->material_request,
            'item_status' => 'Issued',
            'sales_order_no' => $mesProductionOrder->sales_order,
            'transfer_as' => 'Internal Transfer',
            'item_classification' => $workOrder->item_classification,
            'so_customer_name' => $mesProductionOrder->customer,
            'order_type' => $mesProductionOrder->classification,
            'total_outgoing_value' => $rmTotalAmount,
            'total_incoming_value' => $rmTotalAmount,
            'total_amount' => $rmTotalAmount,
            'items' => $apiItems,
        ]);

        return [
            'payload' => $payload,
            'item_codes' => $itemCodes,
            'rm_bin_expectations' => $rmBinExpectations,
            'fg_bin_expectations' => $fgBinExpectations,
            'production_order_items' => $productionOrderItems,
            'rm_total_amount' => $rmTotalAmount,
        ];
    }

    protected function buildDetailRow(array $fields)
    {
        return $this->filterNullValues(array_merge(
            ['doctype' => self::DOCTYPE_STOCK_ENTRY_DETAIL],
            $fields
        ));
    }

    protected function filterNullValues(array $data)
    {
        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }
}
