<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BranchUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'branch_users';

    protected $fillable = [
        'bank_id',
        'branch_id',
        'officer_id',
        'name',
        'email',
        'mobile_number',
        'password',
        'designation',
        'pan_number',
        'tan_number',
        'age',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'age' => 'integer',
        ];
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(BankMasterUser::class, 'bank_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function deeds(): HasMany
    {
        return $this->hasMany(Deed::class, 'created_by_user_id');
    }
}
