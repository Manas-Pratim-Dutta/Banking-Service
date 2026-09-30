<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use App\Services\Module\FeeCalculation\FeeCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeCalculationController extends Controller
{
    public function index(Request $request, FeeCalculationService $feeService)
    {
        $tokenNo = session('token_no');
        $articleId = session('article_id') ?? request('article_id', '91');

        // Fetch property details entered in previous forms
        $properties = $feeService->getPropertyList($tokenNo);

        /*
        // -------------------------------------------------------------------------------------------------
        // Legacy Validation (from CitizenentryController::stamp_duty):
        // Enforce property addition if required by article_screen_mapping (minorfun_id = 2)
        // -------------------------------------------------------------------------------------------------
        $isPropertyRequired = DB::table('ngdrstab_mst_article_screen_mapping')
            ->where('article_id', $articleId)
            ->where('minorfun_id', 2)
            ->exists();

        if ($isPropertyRequired && $properties->isEmpty()) {
            return redirect()->route('property')->with('error', 'Kindly add Property first.');
        }
        // -------------------------------------------------------------------------------------------------
        */

        $zonalValuation = 0.0;
        $considerationAmount = 0;
        $feeParams = [
            'tatkal_flag'       => 0,
            'home_visit_flag'   => 0,
            'municipality_flag' => 0,
            'metro_city_flag'   => 0,
            'no_of_pages'       => 0,
            'FCA'               => 0,
            'FBR'               => 0,
            'FBG'               => 0,
            'FBQ'               => 0,
            'FAJ'               => 0,
        ];

        if (!empty($tokenNo)) {
            // Populate consideration amount from table: ngdrstab_trn_noc_landdetails, col: consideration_amount
            $nocRecords = DB::table('ngdrstab_trn_noc_landdetails')
                ->where('token_no', $tokenNo)
                ->whereNotNull('consideration_amount')
                ->where('consideration_amount', '!=', '')
                ->select('appno', DB::raw("MAX(CAST(NULLIF(regexp_replace(consideration_amount, '[^0-9.]', '', 'g'), '') AS NUMERIC)) as amt"))
                ->groupBy('appno')
                ->get();

            if ($nocRecords->isNotEmpty()) {
                $nocCons = (float) $nocRecords->sum('amt');
                if ($nocCons > 0) {
                    $considerationAmount = $nocCons;
                }
            }

            $firstPropertyId = $properties->first()['property_id'] ?? null;
            $zonalRes = $feeService->getZonalValuation([
                'token_no'    => $tokenNo,
                'article_id'  => $articleId,
                'property_id' => $firstPropertyId,
            ]);

            if (!empty($zonalRes['success']) && !empty($zonalRes['data'])) {
                $zonalValuation = (float) ($zonalRes['data']['val'] ?? 0);
                if ($considerationAmount == 0) {
                    $cons = (float) ($zonalRes['data']['cons'] ?? 0);
                    if ($cons > 0) {
                        $considerationAmount = $cons;
                    }
                }
                $feeParams = array_merge($feeParams, $zonalRes['data']);
            }
        }

        return view('pages.fee-calculation', [
            'preRegistrationNo'   => $tokenNo,
            'tokenNo'             => $tokenNo,
            'articleId'           => $articleId,
            'zonalValuation'      => $zonalValuation,
            'considerationAmount' => $considerationAmount,
            'feeParams'           => $feeParams,
            'properties'          => $properties,
        ]);
    }
}

