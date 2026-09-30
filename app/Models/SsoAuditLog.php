<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SsoAuditLog extends Model
{
    use HasFactory;

    protected $table = 'sso_audit_logs';

    protected $fillable = [
        'bank_id',
        'branch_id',
        'user_id',
        'deed_id',
        'bank_trans_id',
        'action',
        'payload_preview',
        'ip_address',
        'user_agent',
        'status',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(BankMasterUser::class, 'bank_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(BranchUser::class, 'user_id');
    }

    public function deed(): BelongsTo
    {
        return $this->belongsTo(Deed::class, 'deed_id');
    }
}
