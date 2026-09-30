<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginStatusCitizen extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_loginusers_citizen';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public static function logLogin(array $data)
    {
        $defaults = [
            'logindate' => date('Y/m/d'),
            'logintime' => date('H:i:s'),
            'sessionid' => session_id(),
            'req_ip' => request()->ip(),
            'login_status' => 'LoggedIn',
            'host_ip' => request()->server('SERVER_ADDR'),
        ];
        return static::create(array_merge($defaults, $data));
    }
}
