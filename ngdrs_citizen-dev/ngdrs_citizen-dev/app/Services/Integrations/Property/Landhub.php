<?php

namespace App\Services\Integrations\Property;

use Illuminate\Support\Facades\Http;

class Landhub
{

public static function getZonalValue(array $data): array
{
    try {
        // dd($data);
        $response = Http::asForm()
            ->timeout(15)
            ->post(
                'https://landhub.assam.gov.in/api/index.php/NicApi/dagwise_zonal_value',
                [
                    'apikey'             => 'abcd123haryanasinglesigonapplicationDFFEFSDAFETUVX',
                    'dist_code'          => $data['district'],
                    'subdiv_code'        => $data['subdivision'],
                    'cir_code'           => $data['circle'],
                    'mouza_pargona_code' => $data['mouja'],
                    'vill_code'          => $data['village'],
                    'lot_no'             => $data['lot'],
                    'dag_no'             => $data['dag_no'],
                ]
            );

        // HTTP-level failure
        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Unable to fetch zonal value.',
                'data'    => null,
            ];
        }

        $result = $response->json();

        // API explicitly says no data
        if (is_string($result) && strtoupper(trim($result)) === 'NO DATA FOUND') {
            return [
                'success' => false,
                'message' => 'NO DATA FOUND',
                'data'    => null,
            ];
        }

        // Unexpected response
        if (!is_array($result)) {
            return [
                'success' => false,
                'message' => 'Invalid response received from zonal value API.',
                'data'    => null,
            ];
        }

        // Successful response
        return [
            'success' => true,
            'message' => 'Zonal value fetched successfully.',
            'data'    => $result,
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,
            // 'message' => $e->getMessage(),
            'message' => 'Error while fetching zonal value.',
            'data'    => null,
        ];
    }
}
}