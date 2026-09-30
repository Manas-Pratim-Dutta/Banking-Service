<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BankMasterUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'bank_master_users';

    protected $fillable = [
        'bank_code',
        'bank_name',
        'email',
        'password',
        'phone',
        'head_office_address',
        'notification_email',
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
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'bank_id');
    }

    public function branchUsers(): HasMany
    {
        return $this->hasMany(BranchUser::class, 'bank_id');
    }

    public function deeds(): HasMany
    {
        return $this->hasMany(Deed::class, 'bank_id');
    }
}
