<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'bank_id',
        'branch_code',
        'branch_name',
        'ifsc_code',
        'district',
        'address',
        'contact_phone',
        'contact_email',
        'status',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(BankMasterUser::class, 'bank_id');
    }

    public function branchUsers(): HasMany
    {
        return $this->hasMany(BranchUser::class, 'branch_id');
    }

    public function deeds(): HasMany
    {
        return $this->hasMany(Deed::class, 'branch_id');
    }
}
