<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitizenUserReg extends Model
{
    protected $table = 'ngdrstab_trn_usercitizen_registartion';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public static function existsByUserName($username)
    {
        return static::whereRaw('UPPER(user_name) = ?', [strtoupper($username)])->exists();
    }

    public static function existsByEmail($email)
    {
        return static::whereRaw('UPPER(email_id) = ?', [strtoupper($email)])->exists();
    }

    public static function existsByMobile($mobile)
    {
        return static::where('mobile_no', $mobile)->exists();
    }

    public static function existsByUid($uid)
    {
        return static::where('uid', base64_encode($uid))->exists();
    }

    public static function updatePasswordForUser($username, $hashedPassword)
    {
        \DB::connection('ngprs')->table('ngdrstab_trn_usercitizen_registartion')
            ->where('user_name', $username)
            ->update(['user_pass' => $hashedPassword]);
        \DB::connection('ngprs')->table('ngdrstab_mst_user_citizen')
            ->where('username', $username)
            ->update(['password' => $hashedPassword]);

        return true;
    }

    public static function userExists(string $username): bool
    {
        return self::where('user_name', $username)->exists();
    }

    public static function updateUser(array $data, string $username): bool
    {
        return self::where('user_name', $username)
            ->update($data) > 0;
    }

}
