<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfTaluka extends Model
{
    //Taluka is circle 
    protected $table = 'ngdrstab_conf_admblock5_taluka';
    protected $guarded = [];

    public static function getTalukaId(string $districtId, string $subDivId, string $dharitriTalukaCode): ?int
    {
        return self::where('district_id', $districtId)->where('subdivision_id', $subDivId)->where('basundhara_dharitri_cir_code', $dharitriTalukaCode)->value('taluka_id');
    }

    public static function getTalukaDetails(
        string $districtId,
        string $subDivId,
        string $dharitriTalukaCode
    ): ?object {
        return self::where('district_id', $districtId)
            ->where('subdivision_id', $subDivId)
            ->where(
                'basundhara_dharitri_cir_code',
                $dharitriTalukaCode
            )
            ->select('taluka_id', 'taluka_name_en')
            ->first();
    }
    public static function getTalukaByNocCircleCode(
        int $districtId,
        string $circleCode
    ): ?object {
        return self::where('district_id', $districtId)
            ->where(
                'basundhara_dharitri_cir_code',
                $circleCode
            )
            ->select(
                'taluka_id',
                'taluka_name_en'
            )
            ->first();
    }

    public static function getTaluka(int $districtId, int $subDivId)
    {
        return self::where('district_id', $districtId)->where('subdivision_id', $subDivId)->pluck('taluka_name_en', 'taluka_id')->toArray();
    }

    public static function getBasundharaCircleCode(string $talukaId): ?string
    {
        return self::where('taluka_id', $talukaId)->value('basundhara_dharitri_cir_code');
    }
}
