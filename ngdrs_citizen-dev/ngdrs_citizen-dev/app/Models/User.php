<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $guarded = [];

    public static function getDuplicateConfig()
    {
        return [
            'Table' => 'ngdrstab_mst_user',
            'PrimaryKey' => 'user_id',
            'Fields' => ['username','employee_id','mobile_no']
        ];
    }

    public static function smssend($smsid, $mobile_no, $otp)
    {
        // As in Cake code: just log into ngdrstab_trn_smslogs
        DB::connection('ngprs')->table('ngdrstab_trn_smslogs')
            ->insert(['mobno' => $mobile_no, 'messageid' => $smsid, 'message_time' => DB::raw('now()')]);
        return true;
    }
}
