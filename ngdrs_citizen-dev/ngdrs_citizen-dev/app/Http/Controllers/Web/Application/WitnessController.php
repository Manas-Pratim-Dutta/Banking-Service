<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WitnessField;
use App\Models\Salutation;
use App\Models\Gender;
use App\Models\Occupation;
use App\Services\Module\Party\EkycApiService;
use App\Models\Witness;
class WitnessController extends Controller
{
    public function index()
    {
        $preRegistrationNo = session('token_no');
        $docLang = 'en';
        $existingWitnesses =
            Witness::where('token_no', $preRegistrationNo)
                ->orderBy('id', 'asc')
                ->get();
        $masters =
            $this->getWitnessMasters();
        $fieldList =
            WitnessField::where(
                'display_flag',
                'Y'
            )
                ->orderBy(
                    'order',
                    'ASC'
                )
                ->get();
        $authData =
            session('auth_data');

        $ekycDone =
            !empty($authData);

        return view(
            'pages.witness',
            compact(
                'preRegistrationNo',
                'fieldList',
                'masters',
                'authData',
                'ekycDone',
                'existingWitnesses'
            )
        );
    }
    public function delete($id)
    {
        $witness = Witness::findOrFail($id);

        $witness->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Witness deleted successfully.'
        ]);
    }

    private function getWitnessMasters()
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

    public function addWitness(
        Request $request,
        EkycApiService $ekycApiService
    ) {
        try {

            $witnessData = [

                'witness_type' => 'WITNESS'

            ];

            session()->put(
                'witness_entry',
                $witnessData
            );

            session()->put(
                'ekyc_auth_type',
                'W'
            );

            $ekycResponse =
                $ekycApiService->process();

            return response()->json([

                'success' => true,

                'witness_entry' =>
                    $witnessData,

                'ekyc_auth_type' =>
                    'W',

                'encstr' =>
                    $ekycResponse['encstr'],

                'redirect_url' =>
                    $ekycResponse['redirect_url'],

                'message' =>
                    'Witness received successfully'

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()

            ]);
        }
    }
    public function saveWitness(Request $request)
    {
        try {

            $witnessId =
                $request->witness_id;

            if ($witnessId) {

                $witness =
                    Witness::findOrFail(
                        $witnessId
                    );

            } else {

                $witness =
                    new Witness();
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



            $witness->fill(
                $data
            );

            $witness->save();

            /*
             * Clear eKYC session
             * so next witness can
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

                'witness_id' =>
                    $witness->id

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

}
