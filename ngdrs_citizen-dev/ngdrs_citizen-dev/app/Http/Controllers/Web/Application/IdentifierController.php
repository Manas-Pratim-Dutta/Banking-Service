<?php

namespace App\Http\Controllers\Web\Application;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdentifierField;
use App\Models\Salutation;
use App\Models\Gender;
use App\Models\Occupation;
use App\Services\Module\Party\EkycApiService;
use App\Models\Identifier;
class IdentifierController extends Controller
{
    public function index()
    {
        $preRegistrationNo = session('token_no');
        ;
        $docLang = 'en';
        $existingIdentifier =
            Identifier::where('token_no', $preRegistrationNo)
                ->orderBy('id', 'asc')
                ->get();
        $masters =
            $this->getIdentifierMasters();
        $fieldList =
            IdentifierField::where(
                'display_flag',
                'Y'
            )
                ->orderBy('order')
                ->get();
        $authData =
            session('auth_data');

        $ekycDone =
            !empty($authData);
        return view(
            'pages.identifier',
            compact(
                'preRegistrationNo',
                'fieldList',
                'masters',
                'authData',
                'ekycDone',
                'existingIdentifier'
            )
        );
    }
    private function getIdentifierMasters()
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
            'homevisit_flag' => [
                'Y' => 'Yes',
                'N' => 'No'
            ],

            'is_govt_org' => [
                'Y' => 'Yes',
                'N' => 'No'
            ],

            'salutation' =>
                Salutation::pluck(
                    'salutation_desc_en',
                    'salutation_id'
                )->toArray(),

            'gender_id' =>
                Gender::pluck(
                    'gender_desc_en',
                    'gender_id'
                )->toArray(),

            'occupation_id' =>
                Occupation::pluck(
                    'occupation_name_en',
                    'occupation_id'
                )->toArray(),




        ];
    }
    public function addIdentifier(
        Request $request,
        EkycApiService $ekycApiService
    ) {
        try {

            $identifierData = [

                'identifier_type' => 'Identifier'

            ];

            session()->put(
                'identifier_entry',
                $identifierData
            );

            session()->put(
                'ekyc_auth_type',
                'I'
            );

            $ekycResponse =
                $ekycApiService->process();

            return response()->json([

                'success' => true,

                'identifier_entry' =>
                    $identifierData,

                'ekyc_auth_type' =>
                    'I',

                'encstr' =>
                    $ekycResponse['encstr'],

                'redirect_url' =>
                    $ekycResponse['redirect_url'],

                'message' =>
                    'Identifier received successfully'

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()

            ]);
        }
    }
    public function saveIdentifier(Request $request)
    {
        try {

            $identifierId =
                $request->Identifier_id;

            if ($identifierId) {

                $identifier =
                    Identifier::findOrFail(
                        $identifierId
                    );

            } else {

                $identifier =
                    new Identifier();
            }

            $data =
                $request->all();

            $data['token_no'] =
                session('token_no'); // replace with actual token

            $data['state_id'] =
                config('app.state_id');

            $data['user_id'] =
                auth()->id();

            $data['user_type'] =
                'C';
            $data['homevisit_flag'] =
                \DB::table('ngdrstab_trn_generalinformation')
                    ->where('token_no', session('token_no'))
                    ->value('home_visit_flag');

            if (
                session()->has(
                    'auth_data'
                )
            ) {

                $data['auth_data'] =
                    serialize(
                        session(
                            'auth_data'
                        )
                    );
            }

            $data['ekyc_trans_no'] =
                session(
                    'transaction_no'
                );

            $data['req_ip'] =
                $request->ip();



            $identifier->fill(
                $data
            );

            $identifier->save();

            /*
             * Clear eKYC session
             * so next Identifier can
             * go through eKYC again
             */

            session()->forget([
                'auth_data',
                'transaction_no',
                'party_name',
                'party_father_name',
                'party_address',
                'party_gender',
                'party_age',
                'party_pincode',
                'mobile'
            ]);

            return response()->json([

                'status' => 'SUCCESS',

                'identifier_id' =>
                    $identifier->id

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'status' => 'FAILURE',

                'message' =>
                    $e->getMessage(),

                'line' =>
                    $e->getLine()

            ]);
        }
    }
    public function delete($id)
    {
        $identifier = Identifier::findOrFail($id);

        $identifier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Identifier deleted successfully.'
        ]);
    }
}
