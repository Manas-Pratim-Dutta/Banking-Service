<?php

namespace App\Services\Integrations\Property;

use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
class Noc
{
    /**
     * Fetch external NOC data from the Dharitri API.
     */
    public function fetchNocDetails(string $nocNo, string $dharitriDistrictCode): array
    {
        $url = config('services.land.noc_api.url');
        $apiKey = config('services.land.noc_api.apikey');
        $payload = [
            'apikey' => $apiKey,
            'dist_code' => $dharitriDistrictCode,
            'appno' => $nocNo,
        ];

        $response = Http::timeout(10)
            ->withOptions([
                'verify' => true,
                'connect_timeout' => 3,
            ])
            ->post($url, $payload)
            ->throw();

        return $response->json();
    }


    public function fetchIndustrialLeaseDetails(
        string $nocNo,
        string $dharitriDistrictCode
    ): array {
        $url = config('services.land.noc_ind_lease_api.url');

        $jwtToken = $this->generateJwtToken();

        $payload = [
            'user_name' => 'ngdrs',
            'dist_code' => $dharitriDistrictCode,
            'appno' => $nocNo,
        ];

        $response = Http::timeout(10)
            ->connectTimeout(3)
            ->withToken($jwtToken)
            ->asForm()
            ->post($url, $payload)
            ->throw();

        $decoded = $response->json();

        $landschedule = $decoded['data']['landschedule_data'] ?? [];

        return [
            'responseType' => $decoded['status'] ?? null,

            'data' => array_map(function ($item) {
                return [
                    'appno' => $item['appno'] ?? null,
                    'appslno' => $item['appslno'] ?? '1',

                    // Keep the geographic codes.
                    'distcode' => $item['distcode'] ?? '',
                    'subcode' => $item['subcode'] ?? '',
                    'circode' => $item['circode'] ?? '',
                    'mouzacode' => $item['mouzacode'] ?? '',
                    'lotno' => $item['lotno'] ?? '',
                    'villcode' => $item['villcode'] ?? '',

                    'landclass' => $item['landclass'] ?? '',

                    'pattano' => $item['pattano'] ?? '0',
                    'dagno' => $item['dagno'] ?? '',

                    'bigha' => $item['bigha'] ?? '',
                    'katha' => $item['katha'] ?? '',
                    'lessa' => $item['lessa'] ?? '',
                    'are' => $item['are'] ?? '',
                    'acre' => $item['acre'] ?? '',
                    'sqfeet' => $item['sqfeet'] ?? '',

                    'pattatype' => $item['pattatype'] ?? '',
                    'ganda' => $item['ganda'] ?? '',

                    'northside' => $item['northside'] ?? '',
                    'southside' => $item['southside'] ?? '',
                    'eastside' => $item['eastside'] ?? '',
                    'westside' => $item['westside'] ?? '',

                    'appissuedt' => $item['appissuedt'] ?? null,
                ];
            }, $landschedule),
        ];
    }

    public function generateJwtToken()
    {
        $currentTime = time();

        $key = '3bf93d2a0c2628f8020e8ecc9389db5314d7c0b43980a2d6a772f2f9a990153111b513fbc252152e8e5253e89f1841d49a6917dc7f6db4025f2386a9cd143de0';

        $payload = [
            'iat' => $currentTime,
        ];

        try {
            return JWT::encode($payload, $key, 'HS512');
        } catch (\Exception $e) {
            return 'Error generating token: ' . $e->getMessage();
        }
    }
}
