<?php

namespace App\Http\Controllers\Web\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartyType;
use App\Models\PartyCategory;
use App\Models\PartyCategoryField;
use App\Models\TrnGeneralInformation;
use Illuminate\Support\Facades\Auth;
use App\Libraries\AES;
use App\Models\Salutation;
use App\Models\Gender;
use App\Models\Bank;
use App\Models\Occupation;
use App\Models\Maincast;
use App\Models\PresentationExmp;
use App\Models\IdentificationType;
use App\Models\PartyEntry;
use App\Models\Faith;
use App\Models\EsignSessionState;
use App\Services\Actions\Party\NocLandhubService;

// use App\Models\PartyEntry;
// use App\Models\PropertyDetailsEntry;




class PartyController extends Controller
{
    // public function index()
    // {
    //     return view('pages.party', [
    //         'preRegistrationNo' => 202500021456
    //     ]);
    // }
    public function index(Request $request)
    {
        try {

            $docLang = session('sess_langauge', 'en');
            $token = $preRegistrationNo = session('token_no');
            $article_id = TrnGeneralInformation::getArticleId($token);
            $is_noc = 'N';
            $appno = \DB::table('ngdrstab_trn_property_details_entry')
                ->where('token_no', $token)
                ->value('noc_number');

            if (!empty($appno)) {
                $is_noc = 'Y';
            }


            if ($is_noc === 'Y') {

                $appno = \DB::table('ngdrstab_trn_property_details_entry')
                    ->where('token_no', $token)
                    ->value('noc_number');
                // $appno = 'NOC/24/66469/2026';
                if (!empty($appno)) {
                    //  dd('hi');
                    $alreadyFetched = \DB::table('ngdrstab_trn_noc_partydetails')
                        ->where('token_no', $token)
                        ->where('appno', $appno)
                        ->exists();
                    // dd($alreadyFetched);
                    if (!$alreadyFetched) {
                        $result = $this->processNocData($appno, $token, $request->ip());
                        // dd($result);
                        if ($result['status'] !== 'SUCCESS') {
                            return back()->with('error', $result['message']);
                        }
                    }
                }
            }

            // ------------------ VALIDATION ------------------


            //--------------

            $partytype = PartyType::get_party_typename($article_id);
            $party_category = PartyCategory::get_party_category($docLang);
            $existingParties =
                PartyEntry::where(
                    'token_no',
                    $token
                )
                    ->orderBy(
                        'id',
                        'asc'
                    )
                    ->get();
            $allPartiesById =
                PartyEntry::where('token_no', $token)
                    ->pluck('party_full_name_en', 'id');

            foreach ($existingParties as $party) {

                // $party->viewFields =
                //     PartyCategoryField::get_party_fields(
                //         $party->party_catg_id,
                //         $article_id
                //     );
                $effectiveCategoryId =
                    ($party->party_catg_id == 50)
                    ? 1
                    : $party->party_catg_id;

                $party->viewFields =
                    PartyCategoryField::get_party_fields(
                        $effectiveCategoryId,
                        $article_id
                    );

                $party->party_type_name =
                    $partytype[$party->party_type_id]
                    ?? $party->party_type_id;

                $party->party_category_name =
                    $party_category[$party->party_catg_id]
                    ?? $party->party_catg_id;
                $party->poa_party_name =
                    !empty($party->power_attoney_party_id)
                    ? ($allPartiesById[$party->power_attoney_party_id] ?? null)
                    : null;
            }
            $existingPartyTypeIds =
                $existingParties
                    ->pluck('party_type_id')
                    ->unique()
                    ->values()
                    ->toArray();

            $requiredPartyTypeIds =
                array_keys($partytype);

            $allPartyTypesPresent =
                empty(array_diff($requiredPartyTypeIds, $existingPartyTypeIds));

            $poa_applicable =
                $allPartyTypesPresent ? 'Y' : 'N';


            // ------------------ GET REQUEST ------------------

            if (!$request->isMethod('post')) {


                $partyEntry = session('party_entry');
                // dd($partyEntry);exit;

                $partyFields = [];

                // if (!empty($partyEntry)) {

                //     $partyFields =
                //         PartyCategoryField::get_party_fields(
                //             $partyEntry['party_category_id'] ?? null,
                //             $article_id
                //         );



                // }
                if (!empty($partyEntry)) {

                    $partyEntryArr =
                        is_object($partyEntry)
                        ? (array) $partyEntry
                        : (array) $partyEntry;

                    $categoryId = $partyEntryArr['party_category_id'] ?? null;

                    $effectiveCategoryId =
                        ($categoryId == 50)
                        ? 1
                        : $categoryId;

                    $partyFields =
                        PartyCategoryField::get_party_fields(
                            $effectiveCategoryId,
                            $article_id
                        );

                }

                $masters =
                    $this->getPartyMasters();
                // dd($poa_applicable);
                return view(
                    'pages.party',
                    compact(
                        'preRegistrationNo',
                        'partytype',
                        'party_category',
                        'partyEntry',
                        'partyFields',
                        'masters',
                        'existingParties',
                        'is_noc',
                        'poa_applicable'
                    )
                );
            }



        } catch (\Exception $e) {

            // DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function setPresenter(Request $request)
    {
        $request->validate([
            'party_id' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {

            $partyId = $request->party_id;

            /*
             * Get the selected party first
             */
            $party = PartyEntry::find($partyId);

            if (!$party) {
                return response()->json([
                    'success' => false,
                    'message' => 'Party not found.'
                ], 404);
            }

            /*
             * IMPORTANT:
             * First make all parties N
             */
            PartyEntry::query()
                ->where('token_no', $party->token_no)
                ->update([
                    'is_presenter' => 'N'
                ]);

            /*
             * Then make selected party Y
             */
            $party->update([
                'is_presenter' => 'Y'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presenter updated successfully.',
                'party_id' => $partyId
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'party_id' => 'required|integer',
            'file' => [
                'required',
                'file',
                'max:2048',
                'mimes:pdf,jpg,jpeg,png',
            ],
        ]);

        $partyId = (int) $request->party_id;

        $party = PartyEntry::find($partyId);

        if (!$party) {
            return response()->json([
                'success' => false,
                'message' => 'Party not found.'
            ], 404);
        }

        /*
         * Make absolutely sure this party is eligible
         * for file upload.
         */
        if ((int) $party->pan_form_list !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'File upload is not allowed for this party.'
            ], 422);
        }

        /*
         * Get the uploaded file.
         */
        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file uploaded.'
            ], 422);
        }

        /*
         * Extra server-side size check.
         *
         * 2 MB = 2 * 1024 * 1024 bytes
         */
        if ($file->getSize() > (2 * 1024 * 1024)) {
            return response()->json([
                'success' => false,
                'message' => 'File size must not exceed 2 MB.'
            ], 422);
        }

        /*
         * Generate a safe random filename.
         *
         * NEVER use the original filename directly
         * as the physical filename.
         */
        $extension = strtolower($file->getClientOriginalExtension());

        $allowedExtensions = [
            'pdf',
            'jpg',
            'jpeg',
            'png'
        ];

        if (!in_array($extension, $allowedExtensions, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type.'
            ], 422);
        }

        /*
         * Folder:
         *
         * storage/app/public/party_uploads/
         *     token_no/
         *         party_id/
         */
        $directory = 'party_uploads/'
            . $party->token_no
            . '/'
            . $party->id;

        /*
         * Random filename.
         */
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        /*
         * Save file.
         */
        $path = $file->storeAs(
            $directory,
            $filename,
            'public'
        );

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to save the uploaded file.'
            ], 500);
        }

        /*
         * Delete previous file if one exists.
         *
         * This prevents old uploaded files from accumulating.
         */
        if (!empty($party->uploaded_file)) {

            $oldPath = $party->uploaded_file;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        /*
         * Save relative path in PartyEntry.
         */
        $party->uploaded_file = $path;
        $party->save();

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'party_id' => $party->id,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }

    protected function resolvePartyTypeId(int $articleId, string $flag, int $default): int
    {
        $partyTypeId = \DB::table('ngdrstab_mst_article_partytype_mapping as one')
            ->join('ngdrstab_mst_party_type as two', 'one.party_type_id', '=', 'two.party_type_id')
            ->where('one.article_id', $articleId)
            ->where('two.party_type_flag', $flag)
            ->value('one.party_type_id');

        return $partyTypeId ?: $default;
    }

    public function propertyTypeIdForToken(string $tokenNo): ?int
    {
        $value = \DB::table('ngdrstab_trn_property_details_entry')
            ->where('token_no', $tokenNo)
            ->value('property_type_id');

        return $value !== null ? (int) $value : null;
    }
    protected function processNocData(string $appno, int $tokenNo, string $reqIp): array
    {
        // $articleId = session('article_id');
        $token = $preRegistrationNo = session('token_no');
        $articleId = \DB::table('ngdrstab_trn_generalinformation')
            ->where('token_no', $token)
            ->value('article_id');
        $sellerPartyType = $this->resolvePartyTypeId($articleId, '1', 9);
        $buyerPartyType = $this->resolvePartyTypeId($articleId, '0', 10);

        $propVal = $this->propertyTypeIdForToken($tokenNo);

        $service = app(NocLandhubService::class);
        // dd($propVal);
        if ($propVal == 7) {
            $result = $service->fetchGovtLeaseData($appno, $tokenNo);
        } else {
            $result = $service->fetchStandardNocData($appno);
        }

        $data = $result['data'] ?? [];
        // dd($data);
        if (empty($data)) {
            return [
                'status' => 'FAILURE',
                'message' => 'No data returned from LandHub for the given NOC number.',
            ];
        }

        $nocdata = $data['nocdata'] ?? [];
        $combkey = $nocdata['combkey'] ?? '';
        $resolvedAppno = $nocdata['appno'] ?? $appno;

        \DB::table('ngdrstab_trn_noc_partydetails')
            ->where('token_no', $tokenNo)->where('appno', $appno)->delete();
        \DB::table('ngdrstab_trn_noc_landdetails')
            ->where('token_no', $tokenNo)->where('appno', $appno)->delete();

        $savedPartyIds = [];
        // dd($data);
        // ---------------- Sellers ----------------
        foreach (($data['seller'] ?? []) as $seller) {
            \DB::table('ngdrstab_trn_noc_partydetails')->insert([
                'token_no' => $tokenNo,
                'appno' => $resolvedAppno,
                'combkey' => $combkey,
                'party_type' => 'seller',
                'name' => $seller['sname'] ?? '',
                'fname' => $seller['sfname'] ?? '',
                'prhouse' => $seller['prhouse'] ?? '',
                'prlocality' => $seller['prlocality'] ?? '',
                'prpin' => $seller['prpin'] ?? '',
                'prpoff' => $seller['prpoff'] ?? '',
                'prdist' => $seller['prdist'] ?? '',
                'party_instdate' => now(),
                'slno' => $seller['slno'] ?? null,
            ]);

            $party = new PartyEntry();
            $party->token_no = $tokenNo;
            $party->party_type_id = $sellerPartyType;
            $party->party_full_name_en = $seller['sname'] ?? '';
            $party->father_full_name_en = $seller['sfname'] ?? '';
            $party->address_en = $seller['prlocality'] ?? '';
            $party->pin_code = $seller['prpin'] ?: null;
            $party->pan_no = $seller['pan'] ?? null;
            $party->mobile_no = $seller['phone'] ?? null;
            $party->req_ip = $reqIp;
            $party->salutation_id = 1;
            $party->age = 45;
            $party->occupation_id = 37;
            $party->party_catg_id = 1;
            $party->pan_no = 'EBSB0440K';
            $party->mobile_no = 0000000000;
            $party->gender_id = 1;
            $party->pan_form_list = 5;
            $party->relationship_status = 'F';
            $party->faith_id = '3';
            $party->save();

            $savedPartyIds[] = $party->id;
            // dd($party->getAttributes());
            
        }

        // ---------------- Buyers ----------------
        foreach (($data['buyer'] ?? []) as $buyer) {
            \DB::table('ngdrstab_trn_noc_partydetails')->insert([
                'token_no' => $tokenNo,
                'appno' => $resolvedAppno,
                'combkey' => $combkey,
                'party_type' => 'buyer',
                'name' => $buyer['bname'] ?? '',
                'fname' => $buyer['bfname'] ?? '',
                'prhouse' => $buyer['prhouse'] ?? '',
                'prlocality' => $buyer['prlocality'] ?? '',
                'prpin' => $buyer['prpin'] ?? '',
                'prpoff' => $buyer['prpoff'] ?? '',
                'prdist' => $buyer['prdist'] ?? '',
                'party_instdate' => now(),
                'slno' => $buyer['slno'] ?? null,
            ]);

            $party = new PartyEntry();
            $party->token_no = $tokenNo;
            $party->party_type_id = $buyerPartyType;
            $party->party_full_name_en = $buyer['bname'] ?? '';
            $party->father_full_name_en = $buyer['bfname'] ?? '';
            $party->address_en = $buyer['prlocality'] ?? '';
            $party->pin_code = $buyer['prpin'] ?: null;
            $party->pan_no = $buyer['pan'] ?? null;
            $party->mobile_no = $buyer['phone'] ?? null;
            $party->req_ip = $reqIp;
            $party->salutation_id = 1;
            $party->age = 45;
            $party->occupation_id = 37;
            $party->party_catg_id = 1;
            $party->pan_no = 'EBSB0440K';
            $party->mobile_no = 0000000000;
            $party->gender_id = 1;
            $party->pan_form_list = 5;
            $party->relationship_status = 'F';
            $party->faith_id = '3';
            $party->save();

            $savedPartyIds[] = $party->id;
        }

        // ---------------- Power of Attorney (staging only) ----------------
        foreach (($data['power_of_attorney'] ?? []) as $poa) {
            \DB::table('ngdrstab_trn_noc_partydetails')->insert([
                'token_no' => $tokenNo,
                'appno' => $resolvedAppno,
                'combkey' => $combkey,
                'party_type' => 'seller',
                'name' => $poa['name'] ?? '',
                'poa_flag' => 'Y',
                'pow_att_no' => $poa['pow_att_no'] ?? null,
                'pow_att_date' => $poa['pow_att_date'] ?? null,
                'slno' => $poa['slno'] ?? null,
            ]);
        }

        // ---------------- Land schedule ----------------
        foreach (($data['landschedule'] ?? []) as $land) {
            \DB::table('ngdrstab_trn_noc_landdetails')->insert([
                'token_no' => $tokenNo,
                'appno' => $resolvedAppno,
                'combkey' => $combkey,
                'appslno' => $land['appslno'] ?? null,
                'lncombkey' => $land['lncombkey'] ?? null,
                'bigha' => $land['bigha'] ?? null,
                'katha' => $land['katha'] ?? null,
                'lessa' => $land['lessa'] ?? null,
                'are' => $land['are'] ?? null,
                'acre' => $land['acre'] ?? null,
                'sqfeet' => $land['sqfeet'] ?? null,
                'pattano' => $land['pattano'] ?? null,
                'dagno' => $land['dagno'] ?? null,
                'ganda' => $land['ganda'] ?? null,
                'pattatype' => $land['pattatype'] ?? null,
                'land_instdate' => now(),
                'consideration_amount' => $nocdata['considerationamount'] ?? null,
                'district' => $land['district'] ?? null,
                'unique_village_code' => $land['unique_village_code'] ?? null,
            ]);
        }

        // ---------------- Govt lease amount/period (prop_val == 16 only) ----------------
        if ($propVal == 7) {
            \DB::table('ngdrstab_trn_noc_partydetails')->insert([
                'token_no' => $tokenNo,
                'appno' => $resolvedAppno,
                'lease_years' => $nocdata['lease_years'] ?? null,
                'lease_annual_amount' => (int) ($nocdata['considerationamount'] ?? 0),
            ]);
        }

        session()->put('partyCount', count($data['seller'] ?? []) + count($data['buyer'] ?? []) + count($data['power_of_attorney'] ?? []));

        return [
            'status' => 'SUCCESS',
            'party_ids' => $savedPartyIds,
            'seller_count' => count($data['seller'] ?? []),
            'buyer_count' => count($data['buyer'] ?? []),
            'land_count' => count($data['landschedule'] ?? []),
        ];
    }

    public function delete($id)
    {
        $party = PartyEntry::findOrFail($id);

        $party->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Party deleted successfully.'
        ]);
    }


    private function getPartyMasters()
    {
        return [

            'is_executer' => [
                'Y' => 'Yes',
                'N' => 'No'
            ],

            'relationship_status' => [
                'F' => 'Father',
                'M' => 'Mother',
                'S' => 'Spouse'
            ],

            'home_visit_flag' => [
                'Y' => 'Yes',
                'N' => 'No'
            ],

            'is_govt_org' => [
                'Y' => 'Yes',
                'N' => 'No'
            ],

            'salutation_id' =>
                Salutation::pluck(
                    'salutation_desc_en',
                    'salutation_id'
                )->toArray(),

            'gender_id' =>
                Gender::pluck(
                    'gender_desc_en',
                    'gender_id'
                )->toArray(),

            'bank_id' =>
                Bank::pluck(
                    'bank_name_en',
                    'bank_id'
                )->toArray(),

            'occupation_id' =>
                Occupation::pluck(
                    'occupation_name_en',
                    'occupation_id'
                )->toArray(),

            'maincast_id' =>
                Maincast::pluck(
                    'cast_en',
                    'maincast_id'
                )->toArray(),
            'faith_id' =>
                Faith::pluck(
                    'faith_name_en',
                    'faith_id'
                )->toArray(),

            'exemption_id' =>
                PresentationExmp::pluck(
                    'desc_en',
                    'exemption_id'
                )->toArray(),

            'identificationtype_id' =>
                IdentificationType::where(
                    'separate_list',
                    'Y'
                )
                    ->where(
                        'party_flag',
                        'Y'
                    )
                    ->pluck(
                        'identificationtype_desc_en',
                        'identificationtype_id'
                    )
                    ->toArray(),

            'pan_form_list' =>
                IdentificationType::where(
                    'separate_list',
                    'N'
                )
                    ->where(
                        'party_flag',
                        'Y'
                    )
                    ->pluck(
                        'identificationtype_desc_en',
                        'identificationtype_id'
                    )
                    ->toArray()

        ];
    }

    public function redirectionPortal()
    {
        $encstrvv =
            session()->get(
                'encstrr'
            );

        return response("
        <!DOCTYPE html>

        <html>

        <head>

            <title>

                Redirecting...

            </title>

            <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>

            <script>

                $(document).ready(function () {

                    console.log(
                        'Document is ready.'
                    );

                    console.log(
                        'Submitting form...'
                    );

                    $('#redirectrtps')
                        .submit();

                });

            </script>

        </head>

        <body>


            <form
                action='https://basundhara.assam.gov.in/rtpsmb/AadhaarAuthentication/verifyAadhaar'
                method='post'
                name='redirectrtps'
                id='redirectrtps'>

                <input
                    type='hidden'
                    name='enc_data'
                    id='enc_data'
                    value='{$encstrvv}'
                />

            </form>

        </body>

        </html>
    ");
    }


    public function decryptData(
        string $encryptedData,
        string $externalDataResponse
    ): array {

        $key =
            "1234567890123456";

        $originalString =
            str_replace(
                "@",
                "/",
                $encryptedData
            );

        $aes =
            new AES(
                $originalString,
                $key
            );

        $decryptedData =
            $aes->decrypt();

        $externalAes =
            new AES(
                $externalDataResponse,
                $key
            );

        $externalDecrypt =
            $externalAes->decrypt();

        $authData =
            json_decode(
                $decryptedData
            );

        $externalData =
            json_decode(
                $externalDecrypt
            );

        return [

            'auth_data' =>
                $authData,

            'external_data' =>
                $externalData

        ];
    }

    public function processEKYCResponse(
        Request $request
    ) {

        try {

            $postData =
                $request->response;

            $transactionNo =
                $request->transaction_no;

            $externalDataResponse =
                $request->external_data_response;


            // DECRYPT

            $response =
                $this->decryptData(
                    $postData,
                    $externalDataResponse
                );

            $authDecryptedData =
                $response['auth_data'];

            $externalDecryptedData =
                $response['external_data'];

            // WRITE SESSION

            $externalData =
                (array) $externalDecryptedData;

            $tokenNo = $externalData['token_no'] ?? null;

            if ($tokenNo) {

                $storedSession = EsignSessionState::where('token_no', $tokenNo)->first();

                if ($storedSession) {

                    $restoredData = unserialize(
                        $storedSession->session_data,
                        ['allowed_classes' => false]
                    );

                    if (is_array($restoredData)) {
                        foreach ($restoredData as $key => $value) {
                            session()->put($key, $value);
                        }
                    }

                    $storedSession->delete();
                }
            }

            // RESTORE LOGIN

            if (
                !empty(
                $externalData['user_id']
            )
            ) {

                Auth::loginUsingId(
                    $externalData['user_id']
                );

                $request
                    ->session()
                    ->regenerate();

            }

            // RESTORE REQUIRED SESSION DATA

            session()->put(
                'party_entry',
                $externalData['party_entry'] ?? null
            );

            session()->put(
                'ekyc_auth_type',
                $externalData['ekyc_auth_type'] ?? null
            );

            $kycData =
                $authDecryptedData->kycData;

            $authData = [];

            if (
                isset(
                $kycData->ekyc_type
            )
            ) {

                $kycType =
                    $kycData->ekyc_type;

                // ================= AADHAAR =================

                if ($kycType == 'AADHAAR') {

                    $authData = [

                        'auth_type' =>
                            $kycData->ekyc_type,

                        'masked_id' =>
                            $kycData->id_mask,

                        'ekyc_token' =>
                            $kycData->ekyc_token,

                        'id_ref_no' =>
                            $kycData->id_ref_no,

                        'applicant_name_eng' =>
                            $kycData->name_eng,

                        'father_name' =>
                            $kycData->f_name,

                        'dob' =>
                            $kycData->dob,

                        'gender' =>
                            $kycData->gender,

                        'address' =>
                            $kycData->address,

                        'street' =>
                            $kycData->street,

                        'landmark' =>
                            $kycData->land_mark,

                        'city' =>
                            $kycData->city,

                        'pincode' =>
                            $kycData->pin,

                        'district' =>
                            $kycData->district,

                        'state' =>
                            $kycData->state,

                        'country' =>
                            $kycData->country,

                        'mobile' =>
                            $kycData->mobile,

                        'photo_link' =>
                            $kycData->photo_link

                    ];

                    session()->put(
                        'party_name',
                        $authData['applicant_name_eng']
                    );
                    session()->put(
                        'party_mobile',
                        $authData['mobile']
                    );

                    session()->put(
                        'party_father_name',
                        $authData['father_name']
                    );

                    session()->put(
                        'party_address',
                        $authData['address']
                    );

                    session()->put(
                        'party_pincode',
                        $authData['pincode']
                    );
                    session()->put(
                        'party_gender',
                        $authData['gender']
                    );

                    session()->put(
                        'party_age',
                        $this->calculateAge(
                            $authData['dob']
                        )
                    );

                    session()->put(
                        'ekyc_type',
                        'AADHAR'
                    );

                    session()->put(
                        'image',
                        $authData['photo_link']
                    );
                }

                // ================= PAN =================
                elseif (
                    $kycType == 'PAN'
                ) {

                    $authData = [

                        'auth_type' =>
                            $kycData->ekyc_type,

                        'masked_id' =>
                            $kycData->id_mask,

                        'applicant_name_eng' =>
                            $kycData->name_eng,

                        'ekyc_token' =>
                            $kycData->ekyc_token,

                        'id_ref_no' =>
                            $kycData->id_ref_no,

                        'trans_id' =>
                            $kycData->trans_id,

                        'dob' =>
                            $kycData->dob,

                        'gender' =>
                            $kycData->gender,

                        'f_name' =>
                            $kycData->f_name,

                        'address' =>
                            $kycData->address

                    ];

                    session()->put(
                        'party_name',
                        $authData['applicant_name_eng']
                    );

                    session()->put(
                        'party_father_name',
                        $authData['f_name']
                    );

                    session()->put(
                        'party_address',
                        $authData['address']
                    );
                    session()->put(
                        'party_gender',
                        $authData['gender']
                    );

                    session()->put(
                        'party_age',
                        $this->calculateAge(
                            $authData['dob']
                        )
                    );

                    session()->put(
                        'ekyc_type',
                        'PAN'
                    );
                }
            }
            // dd($authData);
            // exit;
            session()->put(
                'auth_data',
                $authData
            );

            session()->put(
                'transaction_no',
                $transactionNo
            );

            $param =
                session()->get(
                    'ekyc_auth_type'
                );

            if ($param == 'P') {

                return redirect(
                    '/party'
                );
            }

            if ($param == 'W') {

                return redirect()->route(
                    'witness'
                );
            }

            if ($param == 'I') {

                return redirect()->route(
                    'identifier'
                );
            }

            if ($param == 'VKYC') {

                return redirect()->route(
                    'vkyc.index'
                );
            }

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()

            ]);
        }
    }
    private function calculateAge($dob)
    {
        if (empty($dob)) {
            return null;
        }

        try {

            return \Carbon\Carbon::parse($dob)
                ->age;

        } catch (\Exception $e) {

            return null;

        }
    }
}
