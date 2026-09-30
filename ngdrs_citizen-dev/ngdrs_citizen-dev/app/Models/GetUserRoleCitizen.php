<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class GetUserRoleCitizen extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_userroles_citizen';
    protected $primaryKey = 'id'; // change if different
    public $timestamps = false;
    protected $guarded = [];

    public static function saveOrUpdateRole(array $data)
    {
        if (empty($data['username'])) return false;

        $exists = static::where('username', $data['username'])->first();
        if ($exists) {
            $exists->update([
                'created' => $data['created'] ?? now(),
                'req_ip' => $data['req_ip'] ?? request()->ip()
            ]);
            return $exists;
        }

        return static::create($data);
    }
    public static function getRoleIdByUser($user_id)
    {
        return DB::select(
            "
            select distinct 
                B.module_name_en,
                B.url,
                A.role_id
            from ngdrstab_mst_userroles_citizen A
            inner join ngdrstab_mst_module B
                on A.module_id = B.module_id
            where A.user_id = ?
            order by B.module_name_en
            ",
            [$user_id]
        );
    }
    public static function getUserModules($user_id)
    {
        return DB::select(
            "
            select distinct
                m.module_id,
                m.module_name_en,
                m.url,
                ur.role_id
            from ngdrstab_mst_userroles_citizen ur
            inner join ngdrstab_mst_module m
                on m.module_id = ur.module_id
            where ur.user_id = ?
            order by m.module_name_en
            ",
            [$user_id]
        );
    }

    public static function findByUsername($username)
    {
        return static::where('username', $username)->get();
    }
}
