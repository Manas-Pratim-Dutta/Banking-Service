<?php

namespace App\Services;

use App\Models\SsoAuditLog;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Log an SSO attempt or event
     */
    public static function logSsoAttempt(
        ?int $bankId,
        ?int $branchId,
        ?int $userId,
        ?int $deedId,
        string $bankTransId,
        array $payload,
        Request $request,
        string $status = 'SUCCESS',
        string $action = 'SSO_INITIATED'
    ): SsoAuditLog {
        // Redact any extremely sensitive raw secrets in log
        $sanitized = $payload;
        if (isset($sanitized['password'])) {
            unset($sanitized['password']);
        }

        return SsoAuditLog::create([
            'bank_id' => $bankId,
            'branch_id' => $branchId,
            'user_id' => $userId,
            'deed_id' => $deedId,
            'bank_trans_id' => $bankTransId,
            'action' => $action,
            'payload_preview' => json_encode($sanitized, JSON_PRETTY_PRINT),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => $request->userAgent() ?? 'Direct SSO Agent',
            'status' => $status,
        ]);
    }
}
