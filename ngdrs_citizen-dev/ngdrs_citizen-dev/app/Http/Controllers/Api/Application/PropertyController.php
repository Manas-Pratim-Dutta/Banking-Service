<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Models\ConfDistrict;
use App\Models\ConfSubdivision;
use App\Models\MstPropertyTypeFormField;
use App\Models\TrnNocLandDetails;
use App\Models\ConfTaluka;
use App\Models\ConfCircle;
use App\Models\ConfVillage;
use App\Models\MstPropertyTypeClassifications;
use App\Models\MstFinyear;
use App\Models\TrnPropertyDetailsEntry;
use App\Models\TrnValuation;
use App\Models\TrnValuationDetails;
use App\Models\MstUsageItemsList;
use App\Models\ConfListItems;
use App\Models\TrnGeneralInformation;
use Illuminate\Http\Request;
use App\Services\Module\Property\LandApiService;
use App\Services\Actions\Property\PropertyDataAction;
use Illuminate\Validation\ValidationException;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Http;
use App\Services\Integrations\Property\Landhub;
class PropertyController extends Controller
{
    public function propertyTypeChange(Request $request)
    {
        $validate = $request->validate([
            'area_type_id' => ['required', 'string'],
            'property_type_id' => ['required', 'string']
        ]);
        $tokenNo = session('token_no');
        $areaTypeId = $request->area_type_id;
        $propertyTypeId = $request->property_type_id;
        $articleId = TrnGeneralInformation::getArticleId($tokenNo);
        $sectionsHtml = [];
        $classficationExists = MstPropertyTypeClassifications::classficationExists($areaTypeId, $propertyTypeId, $articleId);
        if (!$classficationExists) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'sections' => $sectionsHtml,
                'config' => [
                    'showLocationCard' => true,
                    'showDagPattaContainer' => true,
                    'dagPattaInputMode' => 'MANUAL'
                ]
            ]);
        }
        $fields = MstPropertyTypeFormField::getByContext(
            $propertyTypeId,
            $articleId,
            $areaTypeId
        );
        $showLocationDetailFlag = MstPropertyTypeClassifications::getShowLocationDetailFlag($areaTypeId, $propertyTypeId, $articleId);
        $showDagPattaContainerFlag = MstPropertyTypeClassifications::getshowDagPattaContainerFlag($areaTypeId, $propertyTypeId, $articleId);
        $propertyClassification = MstPropertyTypeClassifications::getClassification($areaTypeId, $propertyTypeId, $articleId);
        // dd($showLocationDetailFlag);
        foreach ($fields as $sectionKey => $sectionFields) {
            $sectionsHtml[$sectionKey] = view(
                'elements.property.property-dynamic-fields',
                ['fields' => $sectionFields]
            )->render();
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'sections' => $sectionsHtml,
            'config' => [
                'showLocationCard' => $showLocationDetailFlag,
                'showDagPattaContainer' => $showDagPattaContainerFlag,
                'dagPattaInputMode' => $propertyClassification->dag_patta_input_mode ?? 'MANUAL'
            ]
        ]);
    }

    public function fetchSubdivisions(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string']
        ]);

        $districtId = $validated['district_id'];
        $subdivions = ConfSubdivision::getAllSubdivisions($districtId);
        return response()->json(['success' => true, 'message' => 'Subdivisions fetched successfully', 'subdivisions' => $subdivions]);
    }

    public function fetchNocDetails(Request $request, LandApiService $service, PropertyDataAction $propertyAction)
    {
        $validated = $request->validate([
            'noc_no' => ['required', 'string'],
            'property_type_id' => ['required', 'string']
        ]);
        $nocNo = $validated['noc_no'];
        $propertyTypeId = $validated['property_type_id'];
        $token = session('token_no');
        $aptExistsforNoc = TrnNocLandDetails::appointmentExists($nocNo);
        if ($aptExistsforNoc) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment already exists for this NOC. Cancel it to reapply.'
            ]);
        }

        $dharitriDistrictCode = TrnGeneralInformation::getDistrict($token);
        $dharitriDistrictId = TrnGeneralInformation::getDistrictId($token);
        if ($propertyTypeId == 7) {
            $noc = $service->nocIndustrialLeaseApi($nocNo, $dharitriDistrictCode);
        } else {
            $noc = $service->nocApi($nocNo, $dharitriDistrictCode);
        }
        // dd($noc);

        //Add mock fields which is presently not available in NOC api
        $noc = $this->addMockPropertyFields($noc);
        // dd($dharitriDistrictCode,$noc,$nocM);

        $dagItems = $noc['data']['raw']['data'] ?? [];

        // Add patta details
        $dagItems = $propertyAction->mapPattaDetails($dagItems);

        // dd($dagItems);
        $tabsHtml = view('elements.property.dag-tab-header', [
            'dags' => $dagItems,
        ])->render();

        $contentHtml = view('elements.property.dag-tab-content', [
            'dags' => $dagItems,
            'showRemoveButton' => false
        ])->render();

        return response()->json([
            'success' => $noc['success'],
            'noc' => $noc,
            'tabs' => $tabsHtml,
            'content' => $contentHtml,
            'message' => $noc['message'] ?? ''
        ]);
    }

    /**
     * Add mock valuation fields to each NOC data row for testing.
     *
     * @param array $noc
     * @return array
     */
    public function addMockPropertyFields(array $noc): array
    {
        if (!isset($noc['data']['raw']['data']) || !is_array($noc['data']['raw']['data'])) {
            return $noc;
        }

        foreach ($noc['data']['raw']['data'] as &$row) {
            $row['landvaluation'] = rand(500000, 5000000);
            $row['zonalvaluerate'] = rand(500000, 5000000);
            $row['zonename'] = 'Main Road Within 100M';
            $row['zonalclass'] = 'Trade/Commercial site – I';
            $row['dharitreeclass'] = 'Trade Site - I';
        }

        unset($row);

        return $noc;
    }
    public function fetchCircle(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string'],
            'subdiv_id' => ['required', 'string']
        ]);
        $districtId = $validated['district_id'];
        $subdivId = $validated['subdiv_id'];
        $circle = ConfTaluka::getTaluka($districtId, $subdivId);
        return response()->json(['success' => true, 'message' => 'Circle Fetched Successfully', 'circle' => $circle]);
    }
    public function fetchMouja(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string'],
            'subdiv_id' => ['required', 'string'],
            'circle_id' => ['required', 'string']
        ]);
        $districtId = $validated['district_id'];
        $subdivId = $validated['subdiv_id'];
        $circleId = $validated['circle_id'];
        $mouja = ConfCircle::getMouja($districtId, $subdivId, $circleId);
        return response()->json(['success' => true, 'message' => 'Circle Fetched Successfully', 'mouja' => $mouja]);
    }
    public function fetchVillage(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string'],
            'subdiv_id' => ['required', 'string'],
            'circle_id' => ['required', 'string'],
            'mouja_id' => ['required', 'string']
        ]);
        $districtId = $validated['district_id'];
        $subdivId = $validated['subdiv_id'];
        $circleId = $validated['circle_id'];
        $moujaId = $validated['mouja_id'];
        $village = ConfVillage::getVillage($districtId, $subdivId, $circleId, $moujaId);
        return response()->json(['success' => true, 'message' => 'Circle Fetched Successfully', 'village' => $village]);
    }

    public function fetchAreaType(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string'],
            'subdiv_id' => ['required', 'string'],
            'circle_id' => ['required', 'string'],
            'mouja_id' => ['required', 'string'],
            'village_id' => ['required', 'string'],
        ]);

        $areaTypeId = ConfVillage::getAreaType(
            $validated['district_id'],
            $validated['subdiv_id'],
            $validated['circle_id'],
            $validated['mouja_id'],
            $validated['village_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Area type fetched successfully',
            'areaType' => $areaTypeId
        ]);
    }

    public function verifyDag(Request $request, LandApiService $service)
    {
        $validated = $request->validate([
            'propertyId' => 'required|integer',
            'areaTypeId' => 'required|integer',
            'districtId' => 'required|integer',
            'subDivId' => 'required|integer',
            'circleId' => 'required|integer',
            'moujaId' => 'required|integer',
            'villageId' => 'required|integer',
            'dagNo' => 'required|integer',
        ]);

        return response()->json(
            $service->verifyDagApi($validated)
        );
    }

    public function saveProperty(Request $request)
    {
        try {
            $validated = $this->validateA6P2($request);
            $tokenNo = session('token_no');
            $villLandType = ConfVillage::getLandType($validated['params']['properties'][0]['village']);
            $finYearId = MstFinyear::getFinYear();
            $usageItems = MstUsageItemsList::getItems()->keyBy('field_key');

            foreach ($validated['params']['properties'] as $property) {
                $landValuation = $property['land_valuation'];
                $valuation = TrnValuation::create([
                    'token_no' => $tokenNo,
                    'state_id' => 18,
                    'district_id' => $property['district'],
                    'subdivision_id' => $property['subdivision'],
                    'taluka_id' => $property['circle'],
                    'circle_id' => $property['mouja'],
                    'village_id' => $property['village'],
                    'developed_land_types_id' => $villLandType,
                    'usage_main_catg_id' => 1,
                    'usage_sub_catg_id' => 2,
                    'usage_sub_sub_catg_id' => 363,
                    'finyear_id' => $finYearId,
                    'created' => now(),
                    'property_type_id' => $property['property_type_id'],
                    'noc_number' => $property['noc_no'],
                    'rounded_val_amt' => $property['land_valuation'],
                    'rate' => $property['zonal_rate']
                ]);


                TrnPropertyDetailsEntry::create([
                    'token_no' => $tokenNo,
                    'finyear_id' => $finYearId,
                    'developed_land_types_id' => $villLandType,
                    'area_type_id' => $property['area_type_id'],
                    'property_type_id' => $property['property_type_id'],
                    'noc_number' => $property['noc_no'],
                    'district_id' => $property['district'],
                    'subdivision_id' => $property['subdivision'],
                    'taluka_id' => $property['circle'],
                    'circle_id' => $property['mouja'],
                    'village_id' => $property['village'],
                    'police_station' => $property['police_station'],
                    'post_office' => $property['post_office'],
                    'pincode' => $property['pin_code'],
                    'property_description' => $property['description_of_property'],
                    'dag_no' => $property['dag_no'],
                    'patta_no' => $property['patta_no'],
                    'val_id' => $valuation->val_id,
                    'boundries_east_en' => $property['boundary_east'],
                    'boundries_west_en' => $property['boundary_west'],
                    'boundries_south_en' => $property['boundary_south'],
                    'boundries_north_en' => $property['boundary_north'],

                    'created' => now(),
                    'updated' => now()
                ]);

                foreach ($property as $fieldKey => $value) {

                    if (!$usageItems->has($fieldKey)) {
                        continue;
                    }

                    $item = $usageItems->get($fieldKey);

                    $valuationDetail = [
                        'item_id' => $item->usage_param_id,
                        'val_id' => $valuation->val_id,
                        'rule_id' => 9,
                    ];

                    switch ($fieldKey) {

                        case 'land_valuation':

                            $valuationDetail['item_type_id'] = 2;
                            $valuationDetail['subrule_id'] = 81;
                            $valuationDetail['arrived_value'] = $landValuation;
                            $valuationDetail['final_value'] = $landValuation;
                            $valuationDetail['rounded_val_amt'] = $landValuation;
                            $valuationDetail['rate'] = $value;

                            break;

                        case 'area_bigha':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 1;

                            break;

                        case 'area_kattha':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 2;

                            break;

                        case 'area_lessa':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 3;

                            break;

                        case 'patta_type':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'zonal_rate':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'zone_name':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'zone_class':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'dharitree_class':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'area_chatak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_gonda':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_bigha_barak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_katha_barak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        default:

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;
                    }

                    TrnValuationDetails::create($valuationDetail);
                }

                /*
                 * This approach iterates over all configured usage items and saves a valuation
                 * detail for each item, regardless of whether that field is actually present
                 * or applicable in the current property/application.
                 *
                 * For example, area_chatak will also be saved in valuation details even when
                 * the property uses a different area unit and chatak is not applicable.
                 *
                 * This approach was used in the previous NGDRS implementation and is kept
                 * here as a reference in case it needs to be reverted to this approach in
                 * the future.
                 */
                // foreach ($usageItems as $fieldKey => $item) {

                //     $value = $property[$fieldKey] ?? null;

                //     $valuationDetail = [
                //         'item_id' => $item->usage_param_id,
                //         'val_id' => $valuation->val_id,
                //         'rule_id' => 9,
                //         'item_value_string' => null,
                //         'item_value' => null,
                //         'item_type_id' => null,
                //         'subrule_id' => null,
                //         'arrived_value' => null,
                //         'final_value' => null,
                //         'rounded_val_amt' => null,
                //         'rate' => null,
                //         'dep_detail' => null,
                //     ];

                //     switch ($fieldKey) {

                //         case 'land_valuation':

                //             $valuationDetail['item_type_id'] = 2;
                //             $valuationDetail['subrule_id'] = 81;
                //             $valuationDetail['arrived_value'] = $value;
                //             $valuationDetail['final_value'] = $value;
                //             $valuationDetail['rounded_val_amt'] = $value;
                //             $valuationDetail['rate'] = $property['zonal_rate'] ?? null;

                //             break;

                //         case 'area_bigha':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;
                //             $valuationDetail['area_unit'] = 1;

                //             break;

                //         case 'area_kattha':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;
                //             $valuationDetail['area_unit'] = 2;
                //             break;

                //         case 'area_lessa':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;
                //             $valuationDetail['area_unit'] = 3;
                //             break;

                //         case 'patta_type':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //             break;

                //         case 'zonal_rate':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //             break;

                //         case 'zone_name':

                //             $valuationDetail['item_value_string'] = $value;

                //             break;
                //         case 'zone_class':

                //             $valuationDetail['item_value_string'] = $value;

                //             break;
                //         case 'dharitree_class':

                //             $valuationDetail['item_value_string'] = $value;

                //             break;
                //         case 'area_chatak':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //             break;
                //         case 'area_gonda':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;
                //         case 'area_bigha_barak':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //         case 'area_katha_barak':

                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //             break;

                //         default:
                //             $valuationDetail['item_type_id'] = 1;
                //             $valuationDetail['item_value'] = $value;

                //             break;
                //     }

                //     TrnValuationDetails::create($valuationDetail);
                // }
            }
            return response()->json([
                'success' => true,
                'message' => 'Property saved successfully.',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Required Field missing',
            ], 422);
        }
    }

    private function convertLandAreaToBigha($bigha, $katha, $lessa)
    {
        $bigha = (float) ($bigha ?? 0);
        $katha = (float) ($katha ?? 0);
        $lessa = (float) ($lessa ?? 0);

        // 20 lessa = 1 katha
        $katha += ($lessa / 20);

        // 5 katha = 1 bigha
        $bigha += ($katha / 5);

        return $bigha;
    }

    private function roundUpToNextHundred($amount)
    {
        return ceil((float) $amount / 100) * 100;
    }
    public function saveManualProperty(Request $request)
    {
        try {

            //    dd($request->all());
            $validated = $this->validateA6P1($request);
            $tokenNo = session('token_no');
            $villLandType = ConfVillage::getLandType($validated['params']['properties'][0]['village']);
            $finYearId = MstFinyear::getFinYear();
            $usageItems = MstUsageItemsList::getItems()->keyBy('field_key');
            foreach ($validated['params']['properties'] as $property) {
                $bighaArea = $this->convertLandAreaToBigha(
                    $property['area_bigha'],
                    $property['area_kattha'],
                    $property['area_lessa']
                );
                $landValuation = $bighaArea * (float) $property['zonal_rate'];
                $roundedLandValuation = $this->roundUpToNextHundred($landValuation);
                // dd($property, $bighaArea, $landValuation);
                // $landValuation = $property['land_valuation'];
                $valuation = TrnValuation::create([
                    'token_no' => $tokenNo,
                    'state_id' => 18,
                    'district_id' => $property['district'],
                    'subdivision_id' => $property['subdivision'],
                    'taluka_id' => $property['circle'],
                    'circle_id' => $property['mouja'],
                    'village_id' => $property['village'],
                    'developed_land_types_id' => $villLandType,
                    'usage_main_catg_id' => 1,
                    'usage_sub_catg_id' => 2,
                    'usage_sub_sub_catg_id' => 363,
                    'finyear_id' => $finYearId,
                    'created' => now(),
                    'property_type_id' => $property['property_type_id'],
                    'noc_number' => $property['noc_no'],
                    'rounded_val_amt' => $roundedLandValuation,
                    'rate' => $property['zonal_rate']
                ]);


                TrnPropertyDetailsEntry::create([
                    'token_no' => $tokenNo,
                    'finyear_id' => $finYearId,
                    'developed_land_types_id' => $villLandType,
                    'area_type_id' => $property['area_type_id'],
                    'property_type_id' => $property['property_type_id'],
                    'noc_number' => $property['noc_no'],
                    'district_id' => $property['district'],
                    'subdivision_id' => $property['subdivision'],
                    'taluka_id' => $property['circle'],
                    'circle_id' => $property['mouja'],
                    'village_id' => $property['village'],
                    'police_station' => $property['police_station'],
                    'post_office' => $property['post_office'],
                    'pincode' => $property['pin_code'],
                    'property_description' => $property['description_of_property'],
                    'dag_no' => $property['dag_no'],
                    'patta_no' => $property['patta_no'],
                    'val_id' => $valuation->val_id,
                    'boundries_east_en' => $property['boundary_east'],
                    'boundries_west_en' => $property['boundary_west'],
                    'boundries_south_en' => $property['boundary_south'],
                    'boundries_north_en' => $property['boundary_north'],

                    'created' => now(),
                    'updated' => now()
                ]);

                foreach ($property as $fieldKey => $value) {

                    if (!$usageItems->has($fieldKey)) {
                        continue;
                    }

                    $item = $usageItems->get($fieldKey);

                    $valuationDetail = [
                        'item_id' => $item->usage_param_id,
                        'val_id' => $valuation->val_id,
                        'rule_id' => 9,
                    ];

                    switch ($fieldKey) {

                        case 'land_valuation':

                            $valuationDetail['item_type_id'] = 2;
                            $valuationDetail['subrule_id'] = 81;
                            $valuationDetail['arrived_value'] = $landValuation;
                            $valuationDetail['final_value'] = $landValuation;
                            $valuationDetail['rounded_val_amt'] = $roundedLandValuation;
                            $valuationDetail['rate'] = $value;

                            break;

                        case 'area_bigha':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 1;

                            break;

                        case 'area_kattha':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 2;

                            break;

                        case 'area_lessa':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;
                            $valuationDetail['area_unit'] = 3;

                            break;

                        case 'patta_type':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'zonal_rate':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'zone_name':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'zone_class':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'dharitree_class':

                            $valuationDetail['item_value_string'] = $value;

                            break;

                        case 'area_chatak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_gonda':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_bigha_barak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        case 'area_katha_barak':

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;

                        default:

                            $valuationDetail['item_type_id'] = 1;
                            $valuationDetail['item_value'] = $value;

                            break;
                    }
                    // dd($valuationDetail);
                    TrnValuationDetails::create($valuationDetail);
                }


            }
            return response()->json([
                'success' => true,
                'message' => 'Property saved successfully.',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Required Field missing',
            ], 422);
        }
    }

    private function validateA6P2(Request $request)
    {
        $validated = $request->validate([
            'params' => ['required', 'array'],

            // Properties / DAGs
            'params.properties' => [
                'required',
                'array',
                'min:1',
            ],

            'params.properties.*' => [
                'required',
                'array',
            ],

            // Common Property-level data
            'params.properties.*.area_type_id' => [
                'required',
                'integer',
            ],

            'params.properties.*.property_type_id' => [
                'required',
                'integer',
            ],

            'params.properties.*.noc_no' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.district' => [
                'required',
                'integer',
            ],

            'params.properties.*.subdivision' => [
                'required',
                'integer',
            ],

            'params.properties.*.circle' => [
                'required',
                'integer',
            ],

            'params.properties.*.mouja' => [
                'required',
                'integer',
            ],

            'params.properties.*.village' => [
                'required',
                'integer',
            ],

            'params.properties.*.police_station' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.post_office' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.pin_code' => [
                'required',
                'digits:6',
            ],

            'params.properties.*.description_of_property' => [
                'required',
                'string',
            ],

            // DAG / Patta
            'params.properties.*.dag_no' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.patta_no' => [
                'required',
                'string',
                'max:255',
            ],

            // Area
            'params.properties.*.area_bigha' => [
                'required',
                'numeric',
                'min:0',
            ],

            'params.properties.*.area_kattha' => [
                'required',
                'numeric',
                'min:0',
            ],

            'params.properties.*.area_lessa' => [
                'required',
                'numeric',
                'min:0',
            ],

            // Patta
            'params.properties.*.patta_type_id' => [
                'required',
                'string',
                'max:255',
            ],

            // Valuation
            'params.properties.*.zonal_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'params.properties.*.land_valuation' => [
                'required',
                'numeric',
                'min:0',
            ],

            'params.properties.*.zone_name' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.zone_class' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.dharitree_class' => [
                'required',
                'string',
                'max:255',
            ],

            // Boundaries
            'params.properties.*.boundary_east' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_west' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_south' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_north' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        // Make sure every DAG has some land area
        foreach ($validated['params']['properties'] as $index => $property) {

            $bigha = (float) $property['area_bigha'];
            $kattha = (float) $property['area_kattha'];
            $lessa = (float) $property['area_lessa'];

            if ($bigha == 0 && $kattha == 0 && $lessa == 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "params.properties.$index" =>
                        "Land area must be greater than zero.",
                ]);
            }
        }

        return $validated;
    }

    private function validateA6P1(Request $request)
    {
        $validated = $request->validate([
            'params' => [
                'required',
                'array',
            ],

            /*
             * Properties / DAGs
             */
            'params.properties' => [
                'required',
                'array',
                'min:1',
            ],

            'params.properties.*' => [
                'required',
                'array',
            ],

            /*
             * Common Property-level data
             */
            'params.properties.*.area_type_id' => [
                'required',
                'integer',
            ],

            'params.properties.*.property_type_id' => [
                'required',
                'integer',
            ],

            'params.properties.*.district' => [
                'required',
                'integer',
            ],

            'params.properties.*.subdivision' => [
                'required',
                'integer',
            ],

            'params.properties.*.circle' => [
                'required',
                'integer',
            ],

            'params.properties.*.mouja' => [
                'required',
                'integer',
            ],

            'params.properties.*.village' => [
                'required',
                'integer',
            ],

            'params.properties.*.police_station' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.post_office' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.pin_code' => [
                'required',
                'digits:6',
            ],

            'params.properties.*.description_of_property' => [
                'required',
                'string',
            ],

            'params.properties.*.noc_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * DAG
             */
            'params.properties.*.dag_no' => [
                'required',
                'string',
                'max:255',
            ],

            'params.properties.*.patta_no' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * Area
             */
            'params.properties.*.zonal_rate' => [
                'required',
                'numeric'
            ],

            'params.properties.*.area_bigha' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'params.properties.*.area_kattha' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'params.properties.*.area_lessa' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
             * Patta
             */
            'params.properties.*.patta_type_id' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * Valuation
             */
            'params.properties.*.land_valuation' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'params.properties.*.zone_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'params.properties.*.zone_class' => [
                'nullable',
                'string',
                'max:255',
            ],

            'params.properties.*.dharitree_class' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * Boundaries
             */
            'params.properties.*.boundary_east' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_west' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_south' => [
                'required',
                'string',
                'max:1000',
            ],

            'params.properties.*.boundary_north' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Make sure every DAG has some land area.
         *
         * At least one of Bigha, Kattha or Lessa
         * must be greater than zero.
         */
        foreach ($validated['params']['properties'] as $index => $property) {

            $bigha = (float) ($property['area_bigha'] ?? 0);
            $kattha = (float) ($property['area_kattha'] ?? 0);
            $lessa = (float) ($property['area_lessa'] ?? 0);

            if ($bigha == 0 && $kattha == 0 && $lessa == 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "params.properties.$index.area" =>
                        "Land area must be greater than zero.",
                ]);
            }
        }

        return $validated;
    }

    public function getSavedProperty(Request $request)
    {
        $tokenNo = session('token_no');
        $properties = TrnPropertyDetailsEntry::getPropertyDetails($tokenNo);
        // dd($properties);
        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    public function getDagDetails(Request $request)
    {
        $data = $request->all();

        $basundharaCodes = Helpers::getBasundharaCodes([
            'districtId' => $data['district'],
            'subDivId' => $data['subdivision'],
            'circleId' => $data['circle'],
            'moujaId' => $data['mouja'],
            'villageId' => $data['village'],
        ]);

        $basundharaCodes['dag_no'] = $data['dag_no'];

        $response = Landhub::getZonalValue($basundharaCodes);

        if (!$response['success']) {
            return response()->json($response);
        }

        $zonalData = $response['data'];
        $pattaTypes = ConfListItems::getItems()
            ->pluck('item_desc_en', 'id')
            ->toArray();
        // dd($pattaTypes);

        $dag = [
            'dag_no' => $data['dag_no'],
            'patta_no' => $data['patta_no'],
            'zonal_rate' => $zonalData['Zonal_Value_Rate'] ?? '',
            'zone_name' => $zonalData['Zone_name'] ?? '',
            'zone_class' => $zonalData['Zonal_class'] ?? '',
            'dharitree_class' => $zonalData['Dharitree_class_english'] ?? '',
        ];

        $tabHtml = view('elements.property.dag-tab-header-manual', [
            'dag' => $dag,
        ])->render();

        $contentHtml = view('elements.property.dag-content-manual', [
            'dag' => $dag,
            'showRemoveButton' => true,
            'pattaTypes' => $pattaTypes,
        ])->render();

        return response()->json([
            'success' => true,
            'tab' => $tabHtml,
            'content' => $contentHtml,
            'message' => 'DAG details fetched successfully.',
        ]);
    }
}