<?php

namespace App\Services\Module\FeeCalculation;

use App\Models\ConfVillage;
use App\Services\Integrations\FeeCalculation\FeeCalculationHttpClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeCalculationService
{
    protected FeeCalculationHttpClient $httpClient;

    public function __construct(FeeCalculationHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Resolve a user-friendly error message from exception.
     */
    protected function resolveErrorMessage(\Throwable $e, string $fallback = 'Unable to connect to fee calculation services.'): string
    {
        if (
            $e instanceof ConnectionException ||
            stripos($e->getMessage(), 'Failed to connect') !== false ||
            stripos($e->getMessage(), 'Connection refused') !== false ||
            stripos($e->getMessage(), 'Connection timed out') !== false ||
            stripos($e->getMessage(), 'cURL error 7') !== false ||
            stripos($e->getMessage(), 'cURL error 28') !== false ||
            stripos($e->getMessage(), 'Network is unreachable') !== false
        ) {
            return 'Unable to connect to fee calculation services. Please verify the service is running or try again later.';
        }

        if ($e instanceof RequestException && $e->response) {
            $body = $e->response->json();
            if (!empty($body['message'])) {
                return (string) $body['message'];
            }
            if (!empty($body['error'])) {
                return (string) $body['error'];
            }
            if (!empty($body['errors']) && is_array($body['errors'])) {
                $first = reset($body['errors']);
                if (is_array($first)) {
                    return (string) reset($first);
                }
                if (is_string($first)) {
                    return $first;
                }
            }
        }

        $rawMessage = $e->getMessage();
        if (
            stripos($rawMessage, 'Failed to connect') !== false ||
            stripos($rawMessage, 'Connection refused') !== false ||
            stripos($rawMessage, 'cURL error') !== false
        ) {
            return 'Unable to connect to fee calculation services. Please try again later.';
        }

        return !empty($rawMessage) ? $rawMessage : $fallback;
    }

    /**
     * Get fee rule checklist.
     */
    public function getFeeRuleChecklist(array $params): array
    {
        try {
            $response = $this->httpClient->getFeeRuleChecklist([
                'article_id' => (string) ($params['article_id'] ?? '91'),
            ]);

            return [
                'success' => ($response['status'] ?? '') === 'success' || !empty($response['success']),
                'message' => $response['message'] ?? 'Fee rule checklist retrieved successfully',
                'data'    => $response['data'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::getFeeRuleChecklist error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
                'data'    => null,
            ];
        }
    }

    /**
     * Get fee rule items.
     */
    public function getFeeRuleItems(array $params): array
    {
        try {
            $response = $this->httpClient->getFeeRuleItems([
                'fee_rule_id'  => (string) $params['fee_rule_id'],
                'article_id'   => (string) ($params['article_id'] ?? '91'),
                'doc_token_no' => (string) ($params['doc_token_no'] ?? '202400004497'),
                'lang'         => (string) ($params['lang'] ?? 'en'),
            ]);

            return [
                'success' => ($response['status'] ?? '') === 'success' || !empty($response['success']),
                'message' => $response['message'] ?? 'Fee rule items retrieved successfully',
                'data'    => $response['data'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::getFeeRuleItems error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
                'data'    => null,
            ];
        }
    }

    /**
     * Fetch dynamic zonal valuation and consideration amount for a token.
     * Ported from CakePHP get_valuation_amt_sd() (CitizenentryController.php:L13537-L13666).
     */
    public function getZonalValuation(array $params): array
    {
        try {
            $tokenNo = (string) ($params['token_no'] ?? session('token_no') ?? '');
            $articleId = (string) ($params['article_id'] ?? session('article_id') ?? '91');
            $propertyId = !empty($params['property_id']) ? (int) $params['property_id'] : null;
            $valId = !empty($params['val_id']) ? (int) $params['val_id'] : null;

            if (empty($tokenNo)) {
                return [
                    'success' => true,
                    'message' => 'No token provided',
                    'data'    => [
                        'val'         => 0.0,
                        'cons'        => 0.0,
                        'party'       => 0,
                        'property_id' => 1,
                        'token_no'    => '',
                    ],
                ];
            }

            $valIds = [];
            $consAmount = 0.0;
            $resolvedPropertyId = $propertyId;

            // 1. If explicit valId passed
            if (!empty($valId)) {
                $valIds[] = $valId;
            }

            // 2. Query property details entry
            if (empty($valIds)) {
                $propQuery = DB::table('ngdrstab_trn_property_details_entry')
                    ->where('token_no', $tokenNo);

                if (!empty($propertyId)) {
                    $propQuery->where('property_id', $propertyId);
                }

                $properties = $propQuery->get();

                if ($properties->isNotEmpty()) {
                    foreach ($properties as $p) {
                        if (!empty($p->val_id)) {
                            $valIds[] = (int) $p->val_id;
                        }
                        if ($consAmount == 0 && !empty($p->consideration_amount)) {
                            $consAmount = (float) $p->consideration_amount;
                        }
                        if (!$resolvedPropertyId && !empty($p->property_id)) {
                            $resolvedPropertyId = (int) $p->property_id;
                        }
                    }
                }

                // If consideration amount not populated from property entry, check ngdrstab_trn_noc_landdetails
                if ($consAmount == 0 && !empty($tokenNo) && is_numeric($tokenNo)) {
                    $nocRecords = DB::table('ngdrstab_trn_noc_landdetails')
                        ->where('token_no', $tokenNo)
                        ->whereNotNull('consideration_amount')
                        ->where('consideration_amount', '!=', '')
                        ->select('appno', DB::raw("MAX(CAST(NULLIF(regexp_replace(consideration_amount, '[^0-9.]', '', 'g'), '') AS NUMERIC)) as amt"))
                        ->groupBy('appno')
                        ->get();

                    if ($nocRecords->isNotEmpty()) {
                        $consAmount = (float) $nocRecords->sum('amt');
                    }
                }
            }

            // 3. Fallback to valuation table directly if no property link found
            if (empty($valIds)) {
                $valRecords = DB::table('ngdrstab_trn_valuation')
                    ->where('token_no', $tokenNo)
                    ->get();
                foreach ($valRecords as $vr) {
                    if (!empty($vr->val_id)) {
                        $valIds[] = (int) $vr->val_id;
                    }
                }
            }

            $totalValuation = 0.0;
            $maxValuation = 0.0;

            foreach ($valIds as $vId) {
                // Check valuation_details for item_type_id = 2 (land/property valuation)
                $details = DB::table('ngdrstab_trn_valuation_details')
                    ->where('val_id', $vId)
                    ->where('item_type_id', 2)
                    ->get();

                if ($details->isNotEmpty()) {
                    foreach ($details as $d) {
                        $itemVal = (float) ($d->final_value ?? $d->rounded_val_amt ?? 0);
                        if ($articleId === '32') {
                            $maxValuation = max($maxValuation, $itemVal);
                            $totalValuation = $maxValuation;
                        } else {
                            $totalValuation += $itemVal;
                        }
                    }
                } else {
                    // Fallback to valuation table rounded_val_amt or final_value
                    $vRow = DB::table('ngdrstab_trn_valuation')->where('val_id', $vId)->first();
                    if ($vRow && !empty($vRow->rounded_val_amt)) {
                        $itemVal = (float) $vRow->rounded_val_amt;
                        if ($articleId === '32') {
                            $maxValuation = max($maxValuation, $itemVal);
                            $totalValuation = $maxValuation;
                        } else {
                            $totalValuation += $itemVal;
                        }
                    }
                }
            }

            // 4. Apply Rounding Rule based on ngdrstab_conf_reg_bool_info (reginfo_id = 46)
            $regConf = DB::table('ngdrstab_conf_reg_bool_info')->where('reginfo_id', 46)->first();
            if ($regConf && strtoupper((string)$regConf->is_boolean) === 'Y' && strtoupper((string)$regConf->conf_bool_value) === 'Y') {
                $roundTo = (float) $regConf->info_value;
                if ($roundTo > 0 && $totalValuation > 0) {
                    $totalValuation = ceil($totalValuation / $roundTo) * $roundTo;
                }
            }

            // 5. Party count
            $partyCount = DB::table('ngdrstab_trn_party_entry_new')
                ->where('token_no', $tokenNo)
                ->when($resolvedPropertyId, fn($q) => $q->where('property_id', $resolvedPropertyId))
                ->count();

            // 6. Dynamic flags (Tatkal, Home Visit, Municipality, Metro, Pages)
            $dynamicFlags = $this->resolveDynamicFeeFlags($tokenNo, $resolvedPropertyId);

            return [
                'success' => true,
                'message' => 'Zonal valuation fetched successfully',
                'data'    => array_merge([
                    'val'         => (float) $totalValuation,
                    'cons'        => (float) $consAmount,
                    'party'       => (int) $partyCount,
                    'property_id' => $resolvedPropertyId ?: 1,
                    'val_ids'     => $valIds,
                    'token_no'    => (string) $tokenNo,
                ], $dynamicFlags),
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::getZonalValuation error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
                'data'    => null,
            ];
        }
    }

    /**
     * Retrieve list of saved properties and their land details for the given application token.
     */
    public function getPropertyList(?string $tokenNo): \Illuminate\Support\Collection
    {
        if (empty($tokenNo) || !is_numeric($tokenNo)) {
            return collect();
        }

        $properties = collect();

        // 1. Query ngdrstab_trn_property_details_entry
        $propEntries = DB::table('ngdrstab_trn_property_details_entry')
            ->where('token_no', $tokenNo)
            ->where(function ($q) {
                $q->whereNull('property_id')
                  ->orWhere('property_id', '!=', 999999);
            })
            ->get();

        // 2. If no valid property entries, check ngdrstab_trn_valuation (legacy compatibility)
        if ($propEntries->isEmpty()) {
            $valEntries = DB::table('ngdrstab_trn_valuation')
                ->where('token_no', $tokenNo)
                ->get();

            foreach ($valEntries as $idx => $v) {
                $valId = $v->val_id ?? null;
                $propId = $v->property_id ?: ($valId ?: ($idx + 1));

                $bigha = 0;
                $kattha = 0;
                $lessa = 0;

                if (!empty($valId)) {
                    $valDetails = DB::table('ngdrstab_trn_valuation_details')
                        ->where('val_id', $valId)
                        ->get();

                    foreach ($valDetails as $vd) {
                        if ($vd->item_id == 3 || $vd->area_unit == 1) {
                            $bigha = (float) $vd->item_value;
                        } elseif ($vd->item_id == 4 || $vd->area_unit == 2) {
                            $kattha = (float) $vd->item_value;
                        } elseif ($vd->item_id == 5 || $vd->area_unit == 3) {
                            $lessa = (float) $vd->item_value;
                        }
                    }
                }

                $properties->push([
                    'property_id'    => (int) $propId,
                    'val_id'         => $valId,
                    'dag_no'         => $v->survey_no ?? null,
                    'patta_no'       => null,
                    'area_bigha'     => $bigha,
                    'area_kattha'    => $kattha,
                    'area_lessa'     => $lessa,
                    'land_valuation' => (float) ($v->rounded_val_amt ?? 0),
                ]);
            }
        } else {
            foreach ($propEntries as $idx => $p) {
                $valId = $p->val_id ?? null;
                $propId = $p->property_id ?: ($valId ?: ($p->id ?? ($idx + 1)));

                $bigha = 0;
                $kattha = 0;
                $lessa = 0;
                $landValuation = (float) ($p->consideration_amount ?? 0);

                if (!empty($valId)) {
                    $valDetails = DB::table('ngdrstab_trn_valuation_details')
                        ->where('val_id', $valId)
                        ->get();

                    foreach ($valDetails as $vd) {
                        if ($vd->item_id == 3 || $vd->area_unit == 1) {
                            $bigha = (float) $vd->item_value;
                        } elseif ($vd->item_id == 4 || $vd->area_unit == 2) {
                            $kattha = (float) $vd->item_value;
                        } elseif ($vd->item_id == 5 || $vd->area_unit == 3) {
                            $lessa = (float) $vd->item_value;
                        } elseif ($vd->item_type_id == 2) {
                            $landValuation = (float) ($vd->final_value ?? $vd->rounded_val_amt ?? $landValuation);
                        }
                    }
                }

                $properties->push([
                    'property_id'    => (int) $propId,
                    'val_id'         => $valId,
                    'dag_no'         => $p->dag_no ?? null,
                    'patta_no'       => $p->patta_no ?? null,
                    'area_bigha'     => $bigha,
                    'area_kattha'    => $kattha,
                    'area_lessa'     => $lessa,
                    'land_valuation' => $landValuation,
                ]);
            }
        }

        return $properties;
    }

    /**
     * Resolve dynamic fee parameter flags (Tatkal, Home Visit, Municipality, Metro, Pages) from DB.
     */
    public function resolveDynamicFeeFlags(string $tokenNo, ?int $propertyId = null, $villageId = null): array
    {
        $tatkalFlag = 0;
        $homeVisitFlag = 0;
        $noOfPages = 0;

        if (!empty($tokenNo) && is_numeric($tokenNo)) {
            $genInfo = DB::table('ngdrstab_trn_generalinformation')
                ->where('token_no', $tokenNo)
                ->first();

            if ($genInfo) {
                $tatkalFlag = strtoupper((string) ($genInfo->tatkal_flag ?? 'N')) === 'Y' ? 1 : 0;
                if (strtoupper((string) ($genInfo->home_visit_flag ?? 'N')) === 'Y') {
                    $homeVisitFlag = 1;
                }
                $noOfPages = (int) ($genInfo->no_of_pages ?? 0);
            }

            if ($homeVisitFlag === 0) {
                $partyHasHomeVisit = DB::table('ngdrstab_trn_party_entry_new')
                    ->where('token_no', $tokenNo)
                    ->where(function ($q) {
                        $q->where('home_visit_flag', 'Y')
                          ->orWhere('home_visit_flag', 'y')
                          ->orWhere('home_visit_flag', '1');
                    })
                    ->exists();
                if ($partyHasHomeVisit) {
                    $homeVisitFlag = 1;
                }
            }
        }

        // Municipality (FBG) and Metro City (FBQ)
        $municipalityFlag = 0;
        $metroCityFlag = 0;

        if (!empty($tokenNo) && is_numeric($tokenNo)) {
            $propQuery = DB::table('ngdrstab_trn_property_details_entry')->where('token_no', $tokenNo);
            if (!empty($propertyId)) {
                $propQuery->where('property_id', $propertyId);
            }
            $prop = $propQuery->first();

            $resolvedVillageId = $villageId ?? ($prop->village_id ?? null);
            $developedLandTypeId = $prop->developed_land_types_id ?? null;
            $corpId = $prop->corp_id ?? null;

            if ($resolvedVillageId) {
                $village = DB::table('ngdrstab_conf_admblock7_village_mapping')
                    ->where('village_id', $resolvedVillageId)
                    ->first();

                if ($village) {
                    $isMunicipal = strtoupper((string) ($village->is_municipal ?? 'N')) === 'Y';
                    $isCorp = $village->is_corporatiion === true || strtoupper((string) ($village->is_corporatiion ?? '')) === 'Y';
                    $hasCorpId = !empty($village->corp_id);
                    $isUrban = ((int) ($village->developed_land_types_id ?? 0) === 1) || ((int) ($developedLandTypeId ?? 0) === 1);

                    if ($isMunicipal || $isCorp || $hasCorpId || $isUrban) {
                        $municipalityFlag = 1;
                    }

                    $isMetro = strtoupper((string) ($village->metro_applicable ?? 'N')) === 'Y' || ($village->metro_applicable == '1');
                    if ($isMetro) {
                        $metroCityFlag = 1;
                    }
                }
            } elseif (((int) ($developedLandTypeId ?? 0) === 1) || !empty($corpId)) {
                $municipalityFlag = 1;
            }
        }

        return [
            'tatkal_flag'       => $tatkalFlag,
            'home_visit_flag'   => $homeVisitFlag,
            'municipality_flag' => $municipalityFlag,
            'metro_city_flag'   => $metroCityFlag,
            'no_of_pages'       => $noOfPages,
            'FCA'               => $tatkalFlag,
            'FBR'               => $homeVisitFlag,
            'FBG'               => $municipalityFlag,
            'FBQ'               => $metroCityFlag,
            'FAJ'               => $noOfPages,
        ];
    }

    /**
     * Resolve context parameters from Monolith database (Option A - 0ms latency).
     */
    public function resolveContextParams(array $requestData): array
    {
        $tokenNo = (string) ($requestData['token_no'] ?? '');
        $propertyId = (int) ($requestData['property_id'] ?? 0);
        $villageId = $requestData['village_id'] ?? null;
        $consAmt = (float) ($requestData['cons_amt'] ?? 0);
        if ($consAmt == 0 && !empty($tokenNo) && is_numeric($tokenNo)) {
            $nocRecords = DB::table('ngdrstab_trn_noc_landdetails')
                ->where('token_no', $tokenNo)
                ->whereNotNull('consideration_amount')
                ->where('consideration_amount', '!=', '')
                ->select('appno', DB::raw("MAX(CAST(NULLIF(regexp_replace(consideration_amount, '[^0-9.]', '', 'g'), '') AS NUMERIC)) as amt"))
                ->groupBy('appno')
                ->get();

            if ($nocRecords->isNotEmpty()) {
                $consAmt = (float) $nocRecords->sum('amt');
            }
        }
        $faa = (float) ($requestData['FAA'] ?? $consAmt);

        // 1. Dynamic flags (Tatkal, Home Visit, Municipality, Metro, Pages)
        $dynamicFlags = $this->resolveDynamicFeeFlags($tokenNo, $propertyId, $villageId);
        $noOfPages = $dynamicFlags['no_of_pages'];
        $tatkalFlag = $dynamicFlags['tatkal_flag'];
        $homeVisitFlag = $dynamicFlags['home_visit_flag'];
        $municipalityFlag = $dynamicFlags['municipality_flag'];
        $metroCityFlag = $dynamicFlags['metro_city_flag'];

        // 2. Party details (counts, claimant)
        $partyCount = 0;
        $claimantCount = 0;
        if (!empty($tokenNo) && is_numeric($tokenNo)) {
            $parties = DB::table('ngdrstab_trn_party_entry_new')
                ->where('token_no', $tokenNo)
                ->get();
            $partyCount = $parties->count();
            $claimantCount = $parties->where('party_type_id', 2)->count();
        }

        // 3. Valuation details (max zonal valuation)
        $maxValuation = 0;
        if (!empty($tokenNo) && is_numeric($tokenNo)) {
            $valQuery = DB::table('ngdrstab_trn_valuation')->where('token_no', $tokenNo);
            if ($propertyId > 0) {
                $valQuery->where('property_id', $propertyId);
            }
            $maxVal = $valQuery->max('rounded_val_amt');
            if ($maxVal !== null) {
                $maxValuation = (float) $maxVal;
            }
        }

        // 4. Village attributes (ulb_type_id, corp_id, is_corporation, developed_land_types_id)
        $ulbTypeId = null;
        $corpId = null;
        $isCorp = false;
        $developedLandTypesId = null;
        if (!empty($villageId)) {
            $village = ConfVillage::where('village_id', $villageId)->first();
            if ($village) {
                $ulbTypeId = $village->ulb_type_id !== null ? (int) $village->ulb_type_id : null;
                $corpId = $village->corp_id !== null ? (int) $village->corp_id : null;
                if (!empty($village->is_corporatiion)) {
                    $isCorp = strtoupper((string) $village->is_corporatiion) === 'Y' || $village->is_corporatiion === true || $village->is_corporatiion == 1;
                }
                $developedLandTypesId = $village->developed_land_types_id !== null ? (int) $village->developed_land_types_id : null;
            }
        }

        $mergedContext = [
            'FAA'                     => max($consAmt, $faa, $maxValuation),
            'FAJ'                     => (int) ($requestData['FAJ'] ?? $noOfPages),
            'FAS'                     => (int) ($requestData['FAS'] ?? $partyCount),
            'FBD'                     => (int) ($requestData['FBD'] ?? $claimantCount),
            'FBY'                     => (float) ($requestData['FBY'] ?? $maxValuation),
            'FBO'                     => (int) ($requestData['FBO'] ?? 0),
            'FCA'                     => (int) ($requestData['FCA'] ?? $tatkalFlag),
            'FBR'                     => (int) ($requestData['FBR'] ?? $homeVisitFlag),
            'FBG'                     => (int) ($requestData['FBG'] ?? $municipalityFlag),
            'FBQ'                     => (int) ($requestData['FBQ'] ?? $metroCityFlag),
            'FBL'                     => (int) ($requestData['FBL'] ?? 0),
            'ulb_type_id'             => $ulbTypeId,
            'corp_id'                 => $corpId,
            'is_corporation'          => $isCorp,
        ];

        // If requestData explicitly contains additional context_params, merge them safely
        if (!empty($requestData['context_params']) && is_array($requestData['context_params'])) {
            foreach ($requestData['context_params'] as $k => $v) {
                // Do not let zero dummy values overwrite resolved positive database values for critical parameters
                if (in_array($k, ['FAJ', 'FAS', 'FBD', 'FBY', 'FAA']) && (empty($v) || (float) $v == 0) && !empty($mergedContext[$k])) {
                    continue;
                }
                $mergedContext[$k] = $v;
            }
        }

        return $mergedContext;
    }

    /**
     * Calculate fees via stateless microservice engine and persist locally in Monolith ACID transaction.
     */
    public function calculateAndPersistPropertyFee(array $requestData): array
    {
        $tokenNo = (string) ($requestData['token_no'] ?? '');
        $propertyId = isset($requestData['property_id']) && $requestData['property_id'] !== '' ? (int) $requestData['property_id'] : 0;
        $articleId = (int) ($requestData['article_id'] ?? 91);
        $feeRuleId = isset($requestData['fee_rule_id']) && $requestData['fee_rule_id'] !== '' ? (int) $requestData['fee_rule_id'] : null;
        $subruleId = isset($requestData['subrule_id']) && $requestData['subrule_id'] !== '' ? (int) $requestData['subrule_id'] : null;
        $villageId = $requestData['village_id'] ?? null;
        $consAmt = (float) ($requestData['cons_amt'] ?? 0);
        $stateId = (int) ($requestData['state_id'] ?? 15);
        $userId = auth()->id() ?? (int) ($requestData['user_id'] ?? 1);
        $reqIp = request()->ip() ?? (string) ($requestData['req_ip'] ?? '127.0.0.1');

        // 1. Resolve enriched context parameters (Option A)
        $contextParams = $this->resolveContextParams($requestData);
        $faa = (float) ($requestData['FAA'] ?? $contextParams['FAA'] ?? $consAmt);

        // 2. Build stateless payload conforming to Section 2.1
        $payload = [
            'token_no'       => $tokenNo,
            'property_id'    => $propertyId,
            'article_id'     => $articleId,
            'fee_rule_id'    => $feeRuleId,
            'subrule_id'     => $subruleId,
            'village_id'     => $villageId,
            'cons_amt'       => $consAmt,
            'FAA'            => $faa,
            'user_id'        => $userId,
            'req_ip'         => $reqIp,
            'state_id'       => $stateId,
            'context_params' => $contextParams,
        ];

        // 3. Call stateless calculation engine
        $response = $this->httpClient->calculateFees($payload);
        $calcData = $response['data'] ?? $response;

        if (empty($calcData) || !isset($calcData['total_fee'])) {
            throw new \RuntimeException($response['message'] ?? 'Fee calculation engine returned an invalid response structure.');
        }

        $totalFee = (float) $calcData['total_fee'];
        $details = $calcData['calculation_details'] ?? [];

        // 4. Commit locally in Monolith DB transaction (ACID)
        $feeCalcId = DB::transaction(function () use ($tokenNo, $propertyId, $articleId, $feeRuleId, $userId, $consAmt, $totalFee, $reqIp, $stateId, $details) {
            // Delete prior calculation records for this token & property
            $oldCalcsQuery = DB::table('ngdrstab_trn_fee_calculation')->where('token_no', $tokenNo);
            if ($propertyId > 0) {
                $oldCalcsQuery->where('property_id', $propertyId);
            }
            $oldCalcs = $oldCalcsQuery->pluck('fee_calc_id');

            if ($oldCalcs->isNotEmpty()) {
                DB::table('ngdrstab_trn_fee_calculation_detail')
                    ->whereIn('fee_calc_id', $oldCalcs)
                    ->delete();

                DB::table('ngdrstab_trn_fee_calculation')
                    ->whereIn('fee_calc_id', $oldCalcs)
                    ->delete();
            }

            // Insert new Master Fee Calculation Record
            $newCalcId = DB::table('ngdrstab_trn_fee_calculation')->insertGetId([
                'token_no'     => $tokenNo,
                'property_id'  => $propertyId,
                'article_id'   => $articleId,
                'fee_rule_id'  => $feeRuleId,
                'user_id'      => $userId,
                'cons_amt'     => $consAmt,
                'final_amt'    => $totalFee,
                'cal_date'     => now(),
                'created'      => now(),
                'req_ip'       => $reqIp,
                'state_id'     => $stateId,
                'user_type'    => 'C',
                'delete_flag'  => 'N',
            ], 'fee_calc_id');

            // Insert Calculation Breakdown Details
            foreach ($details as $detail) {
                $finalValue = (float) ($detail['final_value'] ?? 0);
                DB::table('ngdrstab_trn_fee_calculation_detail')->insert([
                    'fee_calc_id'     => $newCalcId,
                    'fee_rule_id'     => $detail['fee_rule_id'] ?? $feeRuleId,
                    'fee_subrule_id'  => $detail['fee_subrule_id'] ?? null,
                    'fee_item_id'     => $detail['fee_item_id'] ?? null,
                    'fee_item_value'  => (string) $finalValue,
                    'item_type_id'    => $detail['item_type_id'] ?? 2,
                    'final_value'     => $finalValue,
                    'fee_calc_desc'   => $detail['fee_subrule_desc'] ?? null,
                    'min_value'       => $detail['min_value'] ?? null,
                    'max_value'       => $detail['max_value'] ?? null,
                    'created'         => now(),
                    'user_type'       => 'C',
                ]);
            }

            // Link Property Record if property_id > 0
            if ($propertyId > 0) {
                DB::table('ngdrstab_trn_property_details_entry')
                    ->where('property_id', $propertyId)
                    ->update(['fee_calc_id' => $newCalcId]);
            }

            return $newCalcId;
        });

        return [
            'success' => true,
            'message' => 'Fees calculated and persisted successfully',
            'data'    => [
                'fee_calc_id'         => $feeCalcId,
                'token_no'            => $tokenNo,
                'property_id'         => $propertyId,
                'article_id'          => $articleId,
                'total_fee'           => $totalFee,
                'calculation_details' => $details,
            ],
        ];
    }

    /**
     * Calculate fees with rule parameters (entry point for API controller).
     */
    public function calculateFees(array $payload): array
    {
        try {
            return $this->calculateAndPersistPropertyFee($payload);
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::calculateFees error', [
                'payload' => $payload,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
                'data'    => null,
            ];
        }
    }

    /**
     * Calculate Market Value.
     */
    public function calculateMarketValue(array $payload): array
    {
        try {
            $response = $this->httpClient->calculateMarketValue($payload);

            return [
                'success' => ($response['status'] ?? '') === 'success' || !empty($response['success']),
                'message' => $response['message'] ?? 'Market value calculated successfully',
                'data'    => $response['data'] ?? $response,
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::calculateMarketValue error', [
                'payload' => $payload,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
                'data'    => null,
            ];
        }
    }

    /**
     * Apply ex-servicemen exemption.
     */
    public function applyExServicemenExemption(array $params): array
    {
        try {
            $tokenNo = (string) $params['token_no'];
            $serviceNo = (string) $params['service_no'];
            $articleId = (int) ($params['article_id'] ?? 91);
            $currentFee = (float) ($params['current_total_fee'] ?? 0);

            $response = $this->httpClient->applyExServicemenExemption([
                'token_no'          => $tokenNo,
                'service_no'        => $serviceNo,
                'article_id'        => $articleId,
                'current_total_fee' => $currentFee,
            ]);

            $responseData = $response['data'] ?? $response;
            $newTotalFee = $responseData['new_total_fee'] ?? $response['new_fee'] ?? null;
            $exemptionAmt = $responseData['exemption_amount'] ?? 0;

            // Update Monolith local tables (general information and fee calculation)
            if (is_numeric($tokenNo)) {
                DB::table('ngdrstab_trn_generalinformation')
                    ->where('token_no', $tokenNo)
                    ->update([
                        'ex_srcvman_exmp_flag' => 'Y',
                        'service_no'           => $serviceNo,
                    ]);

                if ($newTotalFee !== null) {
                    DB::table('ngdrstab_trn_fee_calculation')
                        ->where('token_no', $tokenNo)
                        ->update([
                            'final_amt' => (float) $newTotalFee,
                        ]);
                }

                if (!empty($responseData['calculation_details']) && is_array($responseData['calculation_details'])) {
                    $feeCalcIds = DB::table('ngdrstab_trn_fee_calculation')
                        ->where('token_no', $tokenNo)
                        ->pluck('fee_calc_id');

                    if ($feeCalcIds->isNotEmpty()) {
                        foreach ($responseData['calculation_details'] as $calcDetail) {
                            if (isset($calcDetail['fee_item_id'])) {
                                $updateCols = [
                                    'final_value'    => (float) ($calcDetail['final_value'] ?? 0),
                                    'fee_item_value' => (string) ($calcDetail['final_value'] ?? 0),
                                ];
                                if (isset($calcDetail['exp_amt'])) {
                                    $updateCols['exp_amt'] = (float) $calcDetail['exp_amt'];
                                }
                                if (!empty($calcDetail['fee_subrule_desc'])) {
                                    $updateCols['fee_calc_desc'] = $calcDetail['fee_subrule_desc'];
                                }
                                DB::table('ngdrstab_trn_fee_calculation_detail')
                                    ->whereIn('fee_calc_id', $feeCalcIds)
                                    ->where('fee_item_id', $calcDetail['fee_item_id'])
                                    ->update($updateCols);
                            }
                        }
                    }
                }
            }

            return [
                'success'          => true,
                'message'          => $response['message'] ?? 'Exemption applied successfully',
                'original_fee'     => $responseData['original_fee'] ?? $currentFee,
                'exemption_amount' => $exemptionAmt,
                'new_total_fee'    => $newTotalFee,
                'new_fee'          => $newTotalFee,
                'total_fee'        => $newTotalFee,
                'data'             => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::applyExServicemenExemption error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
            ];
        }
    }

    /**
     * Remove exemption.
     */
    public function removeExemption(array $params): array
    {
        try {
            $tokenNo = (string) $params['token_no'];

            $this->httpClient->removeExemption([
                'token_no' => $tokenNo,
            ]);

            // Reset ex-servicemen flag in Monolith local table
            if (is_numeric($tokenNo)) {
                DB::table('ngdrstab_trn_generalinformation')
                    ->where('token_no', $tokenNo)
                    ->update([
                        'ex_srcvman_exmp_flag' => 'N',
                    ]);
            }

            return [
                'success' => true,
                'message' => 'Exemption removed successfully',
            ];
        } catch (\Throwable $e) {
            Log::error('FeeCalculationService::removeExemption error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e),
            ];
        }
    }
}
