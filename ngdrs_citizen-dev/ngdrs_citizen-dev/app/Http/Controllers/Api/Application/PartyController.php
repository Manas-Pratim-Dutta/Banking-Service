<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Module\Party\EkycApiService;
use App\Models\PartyEntry;
use App\Models\PartyCategory;
use App\Models\PartyCategoryField;



class PartyController extends Controller
{
    public function getPartyCategory(Request $request)
    {
        $docLang = session('sess_langauge', 'en');
        // $article_id = 91;
        $token = $preRegistrationNo = session('token_no');
        $article_id = \DB::table('ngdrstab_trn_generalinformation')
            ->where('token_no', $token)
            ->value('article_id');
        $party_category = PartyCategory::get_party_category($docLang);

        return response()->json([
            'success' => true,
            'categories' => $party_category
        ]);
    }


    public function addParty(
        Request $request,
        EkycApiService $ekycApiService
    ) {

        try {

            $partyData = [

                'party_type_id' =>
                    $request->party_type_id,

                'party_type_name' =>
                    $request->party_type_name,

                'party_category_id' =>
                    $request->party_category_id,

                'party_category_name' =>
                    $request->party_category_name,

                'exemption_under_88' =>
                    $request->exemption_under_88,


            ];

            session()->put(
                'party_entry',
                $partyData
            );

            session()->put(
                'ekyc_auth_type',
                'P'
            );

            // PROCESS EKYC

            $ekycResponse =
                $ekycApiService->process();

            return response()->json([

                'success' => true,

                'party_entry' =>
                    $partyData,

                'ekyc_auth_type' =>
                    'P',

                'encstr' =>
                    $ekycResponse['encstr'],

                'redirect_url' =>
                    $ekycResponse['redirect_url'],

                'message' =>
                    'Party received successfully'

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }

    }
    public function getPartyFields(Request $request)
    {
        $partyEntry = session('party_entry');

        // Normalize to array regardless of whether it's stdClass or array
        if (is_object($partyEntry)) {
            $partyEntry = (array) $partyEntry;
        }

        $categoryId = $partyEntry['party_category_id'] ?? null;

        if ($categoryId == 50) {
            $categoryId = 1;
        }

        $token = $preRegistrationNo = session('token_no');

        $articleId = \DB::table('ngdrstab_trn_generalinformation')
            ->where('token_no', $token)
            ->value('article_id');

        $partyFields = PartyCategoryField::get_party_fields(
            $categoryId,
            $articleId
        );

        return response()->json([
            'success' => true,
            'fields' => $partyFields,
        ]);
    }

    // public function saveParty(Request $request)
    // {
    //     try {
    //         $partyEntry = session('party_entry');
    //         $categoryId =$partyEntry['party_category_id'] ?? null;
    //         $party_type_id = $partyEntry['party_type_id'] ?? null;
    //         $exemption_under_88 = $partyEntry['exemption_under_88'] ?? null;
    //         $homeVisitFlag = \DB::table('ngdrstab_trn_generalinformation')
    //         ->where('token_no', session('token_no'))
    //         ->value('home_visit_flag');
    //         $party = new PartyEntry();

    //         $party->token_no = session('token_no');
    //         $party->home_visit_flag = $homeVisitFlag;
    //          $party->exemption_under_88 = $exemption_under_88;
    //         $party->party_type_id = $party_type_id;
    //         $party->party_catg_id = $categoryId;
    //         $party->salutation_id = $request->salutation_id;
    //         $party->relationship_status = $request->relationship_status;
    //         $party->party_full_name_en = $request->party_full_name_en;
    //         $party->father_full_name_en = $request->father_full_name_en;
    //         $party->age = $request->age;
    //         $party->gender_id = $request->gender_id;
    //         $party->identificationtype_id = $request->identificationtype_id;
    //         $party->mobile_no = $request->mobile_no;
    //         $party->pan_form_list = $request->pan_form_list;
    //         $party->pan_no = $request->pan_no;
    //         $party->org_name_en = $request->org_name_en;
    //         $party->org_name = $request->org_name;
    //         $party->representative_full_name_en = $request->representative_full_name_en;
    //         $party->occupation_id = $request->occupation_id;
    //         $party->bank_id = $request->bank_id;
    //         $party->tan = $request->tan;
    //         $party->district_id = $request->district_id;
    //         $party->taluka_id = $request->taluka_id;
    //         $party->village_id = $request->village_id;

    //         $party->address_en = $request->address_en;
    //         $party->address2_en = $request->address2_en;
    //         $party->pin_code = $request->pin_code;
    //         $party->faith_id = $request->faith_id;
    //         $party->police_station_name = $request->police_station_name;
    //         $party->post_office_name = $request->post_office_name;

    //         $party->relationship_status = $request->relationship_status;

    //         if (session()->has('auth_data')) {
    //             $party->auth_data = serialize(session('auth_data'));
    //         }

    //         if (session()->has('transaction_no')) {
    //             $party->ekyc_trans_no = session('transaction_no');
    //         }

    //         $party->req_ip = $request->ip();
    //         // dd($party->toArray());
    //         $party->save();
    //         session()->forget([
    //             'auth_data',
    //             'transaction_no',
    //             'party_name',
    //             'party_father_name',
    //             'party_address',
    //             'party_pincode',
    //             'party_gender',
    //             'party_age',
    //             'mobile',
    //             'ekyc_type',
    //             'party_entry'
    //         ]);

    //         return response()->json([
    //             'status' => 'SUCCESS',
    //             'party_id' => $party->id
    //         ]);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status' => 'FAILURE',
    //             'message' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //             'request' => $request->all()
    //         ]);
    //     }
    // }

    public function saveParty(Request $request)
    {
        try {
            $partyEntry = (array) session('party_entry');
            $categoryId = $partyEntry['party_category_id'] ?? null;
            $party_type_id = $partyEntry['party_type_id'] ?? null;
            $exemption_under_88 = $partyEntry['exemption_under_88'] ?? null;

            $homeVisitFlag = \DB::table('ngdrstab_trn_generalinformation')
                ->where('token_no', session('token_no'))
                ->value('home_visit_flag');

            $poaIds = $request->poa_id;

            $savedPartyIds = [];

            if (!empty($poaIds) && is_array($poaIds)) {

                // ===== MULTIPLE POA SELECTIONS =====

                $i = 0;

                foreach ($poaIds as $poa) {

                    $i++;

                    $party = new PartyEntry();

                    $party->token_no = session('token_no');
                    $party->home_visit_flag = $homeVisitFlag;
                    $party->exemption_under_88 = $exemption_under_88;
                    $party->party_type_id = $party_type_id;
                    $party->party_catg_id = $categoryId;
                    $party->salutation_id = $request->salutation_id;
                    $party->relationship_status = $request->relationship_status;
                    $party->party_full_name_en = $request->party_full_name_en;
                    $party->father_full_name_en = $request->father_full_name_en;
                    $party->age = $request->age;
                    $party->gender_id = $request->gender_id;
                    $party->identificationtype_id = $request->identificationtype_id;
                    $party->mobile_no = $request->mobile_no;
                    $party->pan_form_list = $request->pan_form_list;
                    $party->pan_no = $request->pan_no;
                    $party->org_name_en = $request->org_name_en;
                    $party->org_name = $request->org_name;
                    $party->representative_full_name_en = $request->representative_full_name_en;
                    $party->occupation_id = $request->occupation_id;
                    $party->bank_id = $request->bank_id;
                    $party->tan = $request->tan;
                    $party->district_id = $request->district_id;
                    $party->taluka_id = $request->taluka_id;
                    $party->village_id = $request->village_id;

                    $party->address_en = $request->address_en;
                    $party->address2_en = $request->address2_en;
                    $party->pin_code = $request->pin_code;
                    $party->faith_id = $request->faith_id;
                    $party->police_station_name = $request->police_station_name;
                    $party->post_office_name = $request->post_office_name;

                    $party->relationship_status = $request->relationship_status;

                    if ($i == 1) {
                        $party->display_flag = 'Y';
                    } else {
                        $party->presenty_require = 'N';
                        $party->is_executer = 'N';
                        $party->display_flag = 'N';
                    }

                    $party->power_attoney_party_id = $poa;

                    if (session()->has('auth_data')) {
                        $party->auth_data = serialize(session('auth_data'));
                    }

                    if (session()->has('transaction_no')) {
                        $party->ekyc_trans_no = session('transaction_no');
                    }

                    $party->req_ip = $request->ip();

                    $party->save();

                    $savedPartyIds[] = $party->id;

                }

            } else {

                // ===== NO POA: NORMAL SINGLE SAVE =====

                $party = new PartyEntry();

                $party->token_no = session('token_no');
                $party->home_visit_flag = $homeVisitFlag;
                $party->exemption_under_88 = $exemption_under_88;
                $party->party_type_id = $party_type_id;
                $party->party_catg_id = $categoryId;
                $party->salutation_id = $request->salutation_id;
                $party->relationship_status = $request->relationship_status;
                $party->party_full_name_en = $request->party_full_name_en;
                $party->father_full_name_en = $request->father_full_name_en;
                $party->age = $request->age;
                $party->gender_id = $request->gender_id;
                $party->identificationtype_id = $request->identificationtype_id;
                $party->mobile_no = $request->mobile_no;
                $party->pan_form_list = $request->pan_form_list;
                $party->pan_no = $request->pan_no;
                $party->org_name_en = $request->org_name_en;
                $party->org_name = $request->org_name;
                $party->representative_full_name_en = $request->representative_full_name_en;
                $party->occupation_id = $request->occupation_id;
                $party->bank_id = $request->bank_id;
                $party->tan = $request->tan;
                $party->district_id = $request->district_id;
                $party->taluka_id = $request->taluka_id;
                $party->village_id = $request->village_id;

                $party->address_en = $request->address_en;
                $party->address2_en = $request->address2_en;
                $party->pin_code = $request->pin_code;
                $party->faith_id = $request->faith_id;
                $party->police_station_name = $request->police_station_name;
                $party->post_office_name = $request->post_office_name;

                $party->relationship_status = $request->relationship_status;

                if (session()->has('auth_data')) {
                    $party->auth_data = serialize(session('auth_data'));
                }

                if (session()->has('transaction_no')) {
                    $party->ekyc_trans_no = session('transaction_no');
                }

                $party->req_ip = $request->ip();

                $party->save();

                $savedPartyIds[] = $party->id;

            }

            session()->forget([
                'auth_data',
                'transaction_no',
                'party_name',
                'party_father_name',
                'party_address',
                'party_pincode',
                'party_gender',
                'party_age',
                'mobile',
                'ekyc_type',
                'party_entry'
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'party_id' => $savedPartyIds[0] ?? null,
                'party_ids' => $savedPartyIds
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'FAILURE',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request' => $request->all()
            ]);
        }
    }







}
