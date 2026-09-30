<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Deed extends Model
{
    use HasFactory;

    protected $table = 'deeds';

    protected $fillable = [
        'deed_no',
        'bank_id',
        'branch_id',
        'created_by_user_id',
        'borrower_name',
        'borrower_mobile',
        'borrower_pan',
        'property_details',
        'loan_amount',
        'stamp_duty',
        'status',
        'bank_trans_id',
        'sso_redirected_at',
    ];

    protected function casts(): array
    {
        return [
            'loan_amount' => 'decimal:2',
            'stamp_duty' => 'decimal:2',
            'sso_redirected_at' => 'datetime',
        ];
    }

    public static function generateDeedNo(BankMasterUser $bank, Branch $branch): string
    {
        $year = date('Y');
        $count = static::where('branch_id', $branch->id)
            ->whereYear('created_at', $year)
            ->count() + 1;

        $sequence = str_pad($count, 5, '0', STR_PAD_LEFT);
        return strtoupper("{$bank->bank_code}/{$year}/{$branch->branch_code}/{$sequence}");
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(BankMasterUser::class, 'bank_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(BranchUser::class, 'created_by_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SsoAuditLog::class, 'deed_id');
    }
}
