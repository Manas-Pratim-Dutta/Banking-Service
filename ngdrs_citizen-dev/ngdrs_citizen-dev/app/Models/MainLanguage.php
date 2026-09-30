<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MainLanguage extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_language';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public static function getMainList()
    {
        // replicates your join-based list logic
        return \DB::connection('ngprs')->table('ngdrstab_mst_language as m')
            ->join('ngdrstab_conf_language as c', 'c.language_id', '=', 'm.id')
            ->select('m.id','m.language_name')
            ->pluck('language_name', 'id');
    }
     public static function getByStateId($state_id)
    {
        return DB::select(
            "
            select *
            from ngdrstab_mst_language
            where state_id = ?
           
            ",
            [$state_id]
        );
    }

    public static function getStateLang($stateId)
    {
        return static::where('state_id', $stateId)->orderBy('id','asc')->get();
    }
}
