<?php

namespace App\Services\Integrations\Property;

use Illuminate\Support\Facades\Http;

class Dharitri
{
    public function verifyBtrDag(array $payload): array
    {
        return $this->post(
            config('services.land.veriy_dag.btr_api.url'),
            $payload
        );
    }

    public function verifyDharitriDag(array $payload): array
    {
        return $this->post(
            config('services.land.veriy_dag.dharitri_api.url'),
            $payload
        );
    }

    protected function post(string $url, array $payload): array
    {
        $response = Http::timeout(5)
            ->connectTimeout(5)
            ->withoutVerifying()
            ->asForm()
            ->post($url, $payload);

        return [
            'success' => $response->successful(),
            'data' => $response->json(),
        ];
    }
}