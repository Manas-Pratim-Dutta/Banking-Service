<?php

namespace App\Services\Integrations\Noc;

use Illuminate\Support\Facades\Http;

class NocHttpClient
{
    /**
     * Fetch external NOC data from the Dharitri API.
     */
    public function fetch(string $nocNo, string $dharitriDistrictCode): array
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
}
