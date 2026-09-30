<?php

namespace App\Services\Actions\Witness;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Libraries\AES;
use App\Models\EsignSessionState;
class ProcessEKycAction
{
    public function execute(): array
    {
        $transactionNo =
            $this->generateTransactionNo();

        $encryptedSessionData =
            $this->encryptSessionData();

        $postData =
            $this->preparePostData(
                $transactionNo,
                $encryptedSessionData
            );

        $encstr =
            $this->encryptData(
                json_encode($postData)
            );

        Session::put(
            'encstrr',
            $encstr
        );

        return [

            'encstr' =>
                $encstr,

            'redirect_url' =>
                route(
                    'redirection.portal'
                )

        ];
    }

    private function generateTransactionNo(): string
    {
        $lastEntry =
            DB::table(
                'ngdrstab_trn_witness'
            )
                ->orderByDesc('id')
                ->first();

        if (
            !empty($lastEntry) &&
            !empty($lastEntry->ekyc_trans_no)
        ) {

            $parts =
                explode(
                    '/',
                    $lastEntry->ekyc_trans_no
                );

            $lastNumber =
                (int) array_pop($parts);

            $newNumber =
                $lastNumber + 1;

        } else {

            $newNumber = 1;
        }

        return
            'NGDRS/WITNESS/' .
            now()->format('YmdHis') .
            '/' .
            $newNumber;
    }

    private function encryptSessionData(): string
    {
        // Persist the full session so it survives the round trip to the
        // external eKYC provider (cross-domain redirects can drop/replace
        // the session cookie, which is why data was going missing).
        $token = session('token_no');
        EsignSessionState::updateOrCreate(
            ['token_no' => $token],
            [
                'user_id' => auth()->id(),
                'session_data' => serialize(session()->all()),
            ]
        );

        // Existing lightweight payload — unchanged
        $sessionData = [

            'user_id' =>
                auth()->id(),

            'witness_entry' =>
                Session::get('witness_entry'),

            'ekyc_auth_type' =>
                Session::get('ekyc_auth_type'),
            'token_no' =>
                $token

        ];

        $jsonData = json_encode($sessionData);

        $key = "1234567890123456";

        $aes = new AES($jsonData, $key);

        return $aes->encrypt();
    }

    // private function encryptSessionData(): string
    // {
    //     $sessionData = [

    //         'user_id' =>
    //             auth()->id(),

    //         'witness_entry' =>
    //             Session::get(
    //                 'witness_entry'
    //             ),

    //         'ekyc_auth_type' =>
    //             Session::get(
    //                 'ekyc_auth_type'
    //             )

    //     ];

    //     $jsonData =
    //         json_encode(
    //             $sessionData
    //         );

    //     $key =
    //         "1234567890123456";

    //     $aes =
    //         new AES(
    //             $jsonData,
    //             $key
    //         );

    //     return $aes->encrypt();
    // }

    private function preparePostData(
        string $transactionNo,
        string $encryptedData
    ): array {

        return [

            'response_url' => config('app.url') . '/process-ekyc-response',

            'auth_key' =>
                '@2@NGDRS_ekyc&456',

            'service_name' =>
                'NGDRS',

            'additional_ekyc' => [
                'PAN'
            ],

            'external_data' =>
                $encryptedData,

            'transaction_no' =>
                $transactionNo

        ];
    }

    private function encryptData(
        string $json
    ): string {

        $key =
            "1234567890123456";

        $aes =
            new AES(
                $json,
                $key
            );

        $enc =
            $aes->encrypt();

        return str_replace(
            '/',
            '@',
            $enc
        );
    }
}