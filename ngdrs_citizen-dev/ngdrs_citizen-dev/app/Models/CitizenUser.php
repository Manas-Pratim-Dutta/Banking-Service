<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitizenUser extends Authenticatable
{
    protected $table = 'ngdrstab_mst_user_citizen';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $guarded = [];

    public static function insertLoginDetails($userId, $roleId = null, $loginType = 'C')
    {
        $nowDate = Carbon::now()->format('Y/m/d');
        $nowTime = Carbon::now()->format('H:i:s');

        DB::connection('ngprs')->table('ngdrstab_mst_loginusers_citizen')->insert([
            'user_id' => $userId,
            'logindate' => $nowDate,
            'logintime' => $nowTime,
            'sessionid' => session()->getId(),
            'req_ip' => request()->ip(),
            'login_status' => 'LoggedIn',
            'role_id' => $roleId,
            'host_ip' => request()->server('SERVER_ADDR'),
            'login_type' => $loginType
        ]);
    }

    public static function insertUnsuccessfulLogin($userId, $loginType = 'C')
    {
        $nowDate = Carbon::now()->format('Y/m/d');
        $nowTime = Carbon::now()->format('H:i:s');

        DB::connection('ngprs')->table('ngdrstab_mst_loginusers_citizen')->insert([
            'user_id' => $userId,
            'logindate' => $nowDate,
            'logintime' => $nowTime,
            'sessionid' => session_id(),
            'req_ip' => request()->ip(),
            'login_status' => 'Unsuccessful Login',
            'login_type' => $loginType
        ]);
    }
    public static function getByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    public static function getUserId($username)
    {
        return self::where('username', $username)->first()->id;
    }

    public static function updateLoginDetails($userId)
    {
        $nowTime = Carbon::now()->format('H:i:s');
        $today = Carbon::now()->format('Y-m-d');

        DB::connection('ngprs')->table('ngdrstab_mst_loginusers_citizen')
            ->where('user_id', $userId)
            ->where('logindate', $today)
            ->update([
                'login_status' => 'Logged out successfully',
                'logouttime' => $nowTime
            ]);
    }

    public static function userExists(string $username): bool
    {
        return self::where('username', $username)->exists();
    }

    public static function updateUser(array $data, string $username): bool
    {
        return self::where('username', $username)
            ->update($data) > 0;
    }
}
