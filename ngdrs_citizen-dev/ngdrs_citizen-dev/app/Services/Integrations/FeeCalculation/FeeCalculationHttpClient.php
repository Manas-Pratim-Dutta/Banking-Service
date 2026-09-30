<?php

namespace App\Services\Integrations\FeeCalculation;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeeCalculationHttpClient
{
    protected string $baseUrl;

    protected int $timeout;

    protected ?string $serviceToken;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) (config('services.fee_service.url') ?? config('services.fee_calculation.url') ?? env('FEE_SERVICE_URL', 'http://127.0.0.1:8080')), '/');
        $this->timeout = (int) (config('services.fee_service.timeout') ?? config('services.fee_calculation.timeout') ?? env('FEE_SERVICE_TIMEOUT', 10));
        $this->serviceToken = config(
            'services.fee_service.token',
            config('services.fee_calculation.token', env('FEE_SERVICE_TOKEN', env('FEE_SERVICE_PSK', env('X_SERVICE_TOKEN'))))
        );
    }

    /**
     * Build base HTTP client request.
     */
    protected function client(): PendingRequest
    {
        $token = $this->serviceToken ?? config(
            'services.fee_service.token',
            config('services.fee_calculation.token', env('FEE_SERVICE_TOKEN', env('FEE_SERVICE_PSK', env('X_SERVICE_TOKEN'))))
        );

        $headers = [];
        if ($token !== null && $token !== '') {
            $headers['X-Service-Token'] = (string) $token;
        }

        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders($headers)
            ->withOptions([
                'verify' => true,
                'connect_timeout' => 3,
            ])
            ->acceptJson();
    }

    /**
     * Fetch fee rule checklist.
     * Endpoint: POST /api/v1/get-fee-rule-checklist
     */
    public function getFeeRuleChecklist(array $params): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/get-fee-rule-checklist', $params)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation getFeeRuleChecklist failed', [
                'params' => $params,
                'error'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Fetch fee rule items.
     * Endpoint: POST /api/v1/fee-rule-items
     */
    public function getFeeRuleItems(array $params): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/fee-rule-items', $params)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation getFeeRuleItems failed', [
                'params' => $params,
                'error'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate fees.
     * Endpoint: POST /api/v1/calculate-fees
     */
    public function calculateFees(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/calculate-fees', $payload);

            if ($response->header('X-Response-Time')) {
                Log::info('Fee calculation telemetry', [
                    'response_time' => $response->header('X-Response-Time'),
                    'db_queries'    => $response->header('X-DB-Queries'),
                ]);
            }

            $response->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation calculateFees failed', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify and apply ex-servicemen exemption.
     * Endpoint: POST /api/v1/exemptions/ex-serviceman
     */
    public function applyExServicemenExemption(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/exemptions/ex-serviceman', $payload)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation applyExServicemenExemption failed', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate Market Value.
     * Endpoint: POST /api/v1/calculate-mv
     */
    public function calculateMarketValue(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/calculate-mv', $payload)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation calculateMarketValue failed', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check fee service health.
     * Endpoint: GET /api/v1/health
     */
    public function checkHealth(): array
    {
        try {
            $response = $this->client()
                ->get('/api/v1/health')
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External FeeCalculation checkHealth failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Remove exemption for token.
     * Endpoint: DELETE /api/v1/exemptions
     */
    public function removeExemption(array $payload): array
    {
        try {
            $response = $this->client()
                ->send('DELETE', '/api/v1/exemptions', [
                    'json' => $payload,
                ]);

            return $response->json() ?? ['success' => true];
        } catch (\Throwable $e) {
            Log::warning('External FeeCalculation removeExemption failed or not supported by stateless engine', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
            return ['success' => true];
        }
    }
}
