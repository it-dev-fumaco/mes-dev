<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionOrderFeedbackRequest;
use App\Models\MES\ProductionOrder;
use App\Services\ProductionOrderFeedbackService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductionOrderFeedbackController extends Controller
{
    /** @var ProductionOrderFeedbackService */
    protected $productionOrderFeedbackService;

    public function __construct(ProductionOrderFeedbackService $productionOrderFeedbackService)
    {
        $this->productionOrderFeedbackService = $productionOrderFeedbackService;
    }

    public function __invoke(ProductionOrderFeedbackRequest $request, $productionOrder)
    {
        if (! Auth::user()) {
            return response()->json([
                'success' => 0,
                'message' => 'Session Expired. Please login to continue.',
            ]);
        }

        if (Gate::denies('create-production-order-feedback')) {
            return response()->json([
                'success' => 0,
                'message' => 'Unauthorized.',
            ]);
        }

        $mesProductionOrder = ProductionOrder::findByProductionOrder($productionOrder);

        if ($this->productionOrderFeedbackService->shouldRedirectToBundleFeedback($mesProductionOrder)) {
            return redirect(
                '/create_bundle_feedback/' . $productionOrder . '/' . $request->input('fg_completed_qty')
            );
        }

        try {
            $result = $this->productionOrderFeedbackService->create(
                $productionOrder,
                $request->validated()
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage() ?: 'There was a problem create stock entry',
            ]);
        }
    }
}
