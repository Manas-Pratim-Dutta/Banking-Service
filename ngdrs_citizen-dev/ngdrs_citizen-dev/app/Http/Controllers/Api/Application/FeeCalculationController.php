<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Services\Module\FeeCalculation\FeeCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeCalculationController extends Controller
{
    protected FeeCalculationService $feeService;

    public function __construct(FeeCalculationService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Fetch fee rule checklist.
     * POST /api/v1/fee-calculation/rules
     */
    public function getFeeRuleChecklist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'article_id' => 'nullable|string',
        ]);

        $result = $this->feeService->getFeeRuleChecklist($validated);
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Fetch fee rule items.
     * POST /api/v1/fee-calculation/rule-items
     */
    public function getFeeRuleItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fee_rule_id'  => 'required',
            'article_id'   => 'nullable|string',
            'doc_token_no' => 'nullable|string',
            'lang'         => 'nullable|string',
        ]);

        $result = $this->feeService->getFeeRuleItems($validated);
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Fetch dynamic zonal valuation and consideration amount.
     * GET/POST /api/v1/fee-calculation/valuation
     */
    public function getZonalValuation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'    => 'nullable|string',
            'article_id'  => 'nullable|string',
            'property_id' => 'nullable|integer',
            'val_id'      => 'nullable|integer',
        ]);

        $result = $this->feeService->getZonalValuation($validated);
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Calculate fees and persist in Monolith DB.
     * POST /api/v1/fee-calculation/calculate
     */
    public function calculateFees(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'       => 'required',
            'property_id'    => 'nullable|integer',
            'article_id'     => 'required',
            'fee_rule_id'    => 'nullable|integer',
            'subrule_id'     => 'nullable|integer',
            'cons_amt'       => 'nullable|numeric',
            'village_id'     => 'nullable',
            'FAA'            => 'nullable|numeric',
            'state_id'       => 'nullable|integer',
            'context_params' => 'nullable|array',
        ]);

        $result = $this->feeService->calculateFees($request->all());
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Calculate Market Value.
     * POST /api/v1/fee-calculation/calculate-mv
     */
    public function calculateMarketValue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fee_rule_id' => 'required',
            'FAA'         => 'required|numeric',
            'param'       => 'nullable|array',
        ]);

        $result = $this->feeService->calculateMarketValue($request->all());
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Verify and apply ex-servicemen exemption.
     * POST /api/v1/fee-calculation/exemptions/ex-serviceman
     */
    public function applyExServicemenExemption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'          => 'required|string',
            'service_no'        => 'required|string',
            'article_id'        => 'nullable',
            'current_total_fee' => 'nullable|numeric',
        ]);

        $result = $this->feeService->applyExServicemenExemption($request->all());
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Remove exemption.
     * DELETE /api/v1/fee-calculation/exemptions
     */
    public function removeExemption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no' => 'required|string',
        ]);

        $result = $this->feeService->removeExemption($validated);
        $status = !empty($result['success']) ? 200 : 422;

        return response()->json($result, $status);
    }
}
