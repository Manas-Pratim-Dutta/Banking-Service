<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class MstLoginUsersCitizen extends Authenticatable
{
    protected $table = "ngdrstab_mst_loginusers_citizen";
    protected $primaryKey = "id";
    protected $guarded = [];
    public $timestamps = false;
    /**
     * Insert login audit record
     */
    public static function insertLoginDetails(int $userId, ?int $roleId = 1, string $loginType = 'C'): bool
    {
        return self::create([
            'user_id'      => $userId,
            'logindate'    => Carbon::now()->format('Y/m/d'),
            'logintime'    => Carbon::now()->format('H:i:s'),
            'sessionid'    => session()->getId(),
            'req_ip'       => request()->ip(),
            'login_status' => 'LoggedIn',
            'role_id'      => $roleId,
            'host_ip'      => request()->server('SERVER_ADDR'),
            'login_type'   => $loginType
        ]) ? true : false;
    }
}
