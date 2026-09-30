<?php

namespace App\Services\Module\GeneralInfo;

use App\Models\ConfDistrict;
use App\Models\ConfTaluka;
use App\Models\TrnGeneralInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeneralInfoService
{
    public function save(array $data)
    {
        return DB::transaction(function () use ($data) {

            // ✅ Case 1: If token exists → use it
            // ✅ Case 2: Else → generate new
            $token = $data['token_no'] ?? $this->generateToken();

            $bankTransId = session('sso_payload.bank_trans_id');
            // 🧠 Prepare data
            $saveData = [
                'token_no' => $token,
                'article_id' => $data['article'],
                'title_id' => $data['document_title_id'],
                'no_of_pages' => $data['no_of_pages'],
                'doc_execution_type_id' => $data['document_execution_type'],
                'entry_date_india' => $data['entry_date_in_india'],
                'court_order_date' => $data['court_order_date'],
                'exec_date' => date('Y-m-d', strtotime($data['date_of_execution'])),
                'adv_name_en' => $data['advocate_name'],
                'courtcaseflg' => $data['court_case'],
                'courtorderno' => $data['court_order_number'],
                'courtorderdate' => $data['court-case_order_date'],
                'doc_writer_name' => $data['document_writer_name'],

                'ref_doc_no' => $data['reference_doc_no'],
                // 'link_office_id' => $data['link_office_name'],
                // 'link_doc_date' => $data['link_document_date'],
                // 'ref_doc_date' => $data['reference_registered_doc_date'],
                // 'sro_doc_no' => $data['sro_office_document_number'],

                'district_id' => $data['district'],
                'taluka_id' => $data['circle'],
                'office_id' => $data['office_name'],

                'home_visit_flag' => $data['is_home_visit'],
                'home_visit_address' => $data['home_visit_address'],
                'lease_type' => $data['lease_type'] ?? null,

                'bank_trans_id' => $bankTransId,
            ];
            // dd($data, $saveData);
            try {
                // 🔥 Try update or insert
                return TrnGeneralInformation::updateOrCreate(
                    ['token_no' => $token],
                    $saveData
                );
            } catch (QueryException $e) {

                // ⚡ Race condition fallback
                return TrnGeneralInformation::where('token_no', $token)
                    ->update($saveData);
            }
        });
    }
    public function generateToken(): string
    {
        return DB::selectOne("select generate_token_number() as token")->token;
    }

    // public function fetchNocData(string $nocNumber): array
    // {
    //     preg_match('#NOC/(\d+)/#', $nocNumber, $matches);

    //     $districtCode = $matches[1] ?? null;

    //     $payload = [
    //         'apikey'   => config('services.land.noc_api.apikey'),
    //         'dist_code' => $districtCode,
    //         'appno'    => $nocNumber,
    //     ];

    //     $response = Http::withoutVerifying()
    //         ->timeout(10)
    //         ->post(
    //             config('services.land.noc_api.url'),
    //             $payload
    //         );

    //     dd([
    //         'status' => $response->status(),
    //         'body' => $response->body(),
    //     ]);
    // }

    //Running
    // public function fetchNocData(string $nocNumber): array
    // {
    //     preg_match('#NOC/(\d+)/#', $nocNumber, $matches);

    //     $districtCode = $matches[1] ?? null;

    //     $payload = [
    //         'apikey'   => config('services.land.noc_api.apikey'),
    //         'dist_code' => $districtCode,
    //         'appno'    => $nocNumber,
    //     ];

    //     $response = Http::withoutVerifying()
    //         ->timeout(10)
    //         ->post(
    //             config('services.land.noc_api.url'),
    //             $payload
    //         );

    //     if (! $response->successful()) {
    //         return [];
    //     }

    //     $responseData = $response->json();
    //     // dd($responseData);

    //     if (
    //         empty($responseData['data']) ||
    //         empty($responseData['data'][0])
    //     ) {
    //         return [];
    //     }

    //     $landData = $responseData['data'][0];

    //     $district = ConfDistrict::getDistrictByBasundharaCode(
    //         $landData['distcode']
    //     );

    //     $taluka = null;

    //     if ($district) {
    //         $taluka = ConfTaluka::getTalukaByNocCircleCode(
    //             $district->district_id,
    //             $landData['circode']
    //         );
    //     }

    //     return [
    //         'district_id'   => $district?->district_id,
    //         'district_name' => $district?->district_name_en,
    //         'circle_id' => $taluka?->taluka_id,
    //         'circle_name' => $taluka?->taluka_name_en,
    //         'district_code' => $district?->basundhara_dharitri_code,
    //         'circle_code'   => $landData['circode'] ?? null,
    //     ];
    // }

    public function fetchNocData(string $nocNumber): array
    {
        preg_match('#NOC/(\d+)/#', $nocNumber, $matches);

        $districtCode = $matches[1] ?? null;

        $payload = [
            'apikey' => config('services.land.noc_api.apikey'),
            'dist_code' => $districtCode,
            'appno' => $nocNumber,
        ];

        $response = Http::withoutVerifying()
            ->timeout(10)
            ->post(
                config('services.land.noc_api.url'),
                $payload
            );
        // dd($payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Unable to connect to NOC service'
            ];
        }

        $resData = $response->json();

        // API returned business error
        if (isset($resData['msg'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => $resData['msg']
            ];
        }

        // Invalid structure
        if (
            !isset($resData['data']) ||
            !is_array($resData['data']) ||
            empty($resData['data'])
        ) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Unexpected API response format'
            ];
        }

        $landData = $resData['data'][0];

        if (!is_array($landData)) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Invalid land data format'
            ];
        }

        $district = ConfDistrict::getDistrictByBasundharaCode(
            $landData['distcode'] ?? null
        );

        $taluka = null;

        if ($district && !empty($landData['circode'])) {
            $taluka = ConfTaluka::getTalukaByNocCircleCode(
                $district->district_id,
                $landData['circode']
            );
        }

        return [
            'success' => true,
            'data' => [
                'district_id' => $district?->district_id,
                'district_name' => $district?->district_name_en,
                'circle_id' => $taluka?->taluka_id,
                'circle_name' => $taluka?->taluka_name_en,
                'district_code' => $district?->basundhara_dharitri_code,
                'circle_code' => $landData['circode'] ?? null,
            ],
            'message' => null
        ];
    }
}
