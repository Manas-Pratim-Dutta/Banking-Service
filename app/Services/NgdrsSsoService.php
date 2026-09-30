<?php

namespace App\Services;

class NgdrsSsoService
{
    protected string $cipher = 'AES-256-CBC';
    protected string $key;
    protected string $iv;
    protected string $endpoint;

    public function __construct()
    {
        $this->key = config('services.ngdrs.sso_key', '1234567890123456');
        $this->iv = config('services.ngdrs.sso_iv', '1234567890123456');
        $this->endpoint = config('services.ngdrs.sso_endpoint', url('/asmsso'));
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Encrypt JSON payload using AES-256-CBC matching ngdrs_citizen-dev LoginController
     */
    public function encryptPayload(array $payload): string
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        
        $encrypted = openssl_encrypt(
            $json,
            $this->cipher,
            $this->key,
            0,
            $this->iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt SSO payload via AES-256-CBC');
        }

        return $encrypted; // Standard Base64 encoded string
    }

    /**
     * Decrypt AES-256-CBC string matching ngdrs_citizen-dev LoginServices::decryptData
     */
    public function decryptPayload(string $encryptedBase64): ?array
    {
        $decrypted = openssl_decrypt(
            $encryptedBase64,
            $this->cipher,
            $this->key,
            0,
            $this->iv
        );

        if ($decrypted === false) {
            return null;
        }

        $json = json_decode(trim($decrypted), true);

        return is_array($json) ? $json : null;
    }
}
