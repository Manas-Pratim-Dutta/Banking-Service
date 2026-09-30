<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TrnGeneralInformation extends Model
{
    protected $table = 'ngdrstab_trn_generalinformation';
    public $timestamps = false; // 🔥 ADD THIS
    protected $guarded = [];

    protected $fillable = [
        'token_no',
        'article_id',
        'title_id',
        'no_of_pages',
        'doc_execution_type_id',
        'entry_date_india',
        'court_order_date',
        'exec_date',
        'adv_name_en',
        'courtcaseflg',
        'courtorderno',
        'courtoderdate',
        'doc_writer_name',
        'ref_doc_no',
        'district_id',
        'taluka_id',
        'office_id',
        'home_visit_flag',
        'home_visit_address',
        'lease_type'
    ];

    public static function getDistrict(string $token)
    {
        return DB::table('ngdrstab_trn_generalinformation as gen')
            ->join('ngdrstab_conf_admblock3_district as district', 'district.district_id', '=', 'gen.district_id')
            ->where('gen.token_no', $token)
            ->value('district.basundhara_dharitri_code');
    }
    
    public static function getDistrictId(string $token)
    {
        return self::where('token_no', $token)->value('district_id');
    }

    public static function getDistrictDetail(?string $token)
    {
        if (empty($token)) {
            return [];
        }

        return DB::table('ngdrstab_conf_admblock3_district as dist')
            ->join(
                'ngdrstab_trn_generalinformation as gen',
                'gen.district_id',
                '=',
                'dist.district_id'
            )
            ->where('gen.token_no', $token)
            ->pluck('dist.district_name_en', 'dist.district_id')
            ->toArray();
    }

    public static function getArticleId(string $token)
    {
        return self::where('token_no', $token)->value('article_id');
    }
}
