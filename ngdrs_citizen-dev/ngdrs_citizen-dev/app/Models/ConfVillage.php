<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfVillage extends Model
{
    protected $table = 'ngdrstab_conf_admblock7_village_mapping';

    public static function getVillageId(
        string $districtId,
        string $subDivId,
        string $talukaId,
        string $circleId,
        string $dharitriLotNo,
        string $dharitriVillageCode
    ): ?int {
        return self::where('district_id', $districtId)->where('subdivision_id', $subDivId)->where('taluka_id', $talukaId)
            ->where('circle_id', $circleId)->where('basundhara_dharitri_lots_no', $dharitriLotNo)->where('basundhara_dharitri_village_code', $dharitriVillageCode)->value('village_id');
    }

    public static function getVillageDetails(
        string $districtId,
        string $subDivId,
        string $talukaId,
        string $circleId,
        string $dharitriLotNo,
        string $dharitriVillageCode
    ): ?object {
        return self::where('district_id', $districtId)
            ->where('subdivision_id', $subDivId)
            ->where('taluka_id', $talukaId)
            ->where('circle_id', $circleId)
            ->where(
                'basundhara_dharitri_lots_no',
                $dharitriLotNo
            )
            ->where(
                'basundhara_dharitri_village_code',
                $dharitriVillageCode
            )
            ->select('village_id', 'village_name_en', 'developed_land_types_id')
            ->first();
    }

    public static function getVillage($districtId, $subdivId, $circleId, $moujaId)
    {
        return self::where('district_id', $districtId)->where('subdivision_id', $subdivId)->where('taluka_id', $circleId)->where('circle_id', $moujaId)->pluck('village_name_en', 'village_id')->toArray();
    }
    public static function getAreaType($districtId, $subdivId, $circleId, $moujaId, $villageId)
    {
        return self::where('district_id', $districtId)
            ->where('subdivision_id', $subdivId)
            ->where('taluka_id', $circleId)
            ->where('circle_id', $moujaId)
            ->where('village_id', $villageId)
            ->join(
                'ngdrstab_mst_developed_land_types as dlt',
                'dlt.developed_land_types_id',
                '=',
                'ngdrstab_conf_admblock7_village_mapping.developed_land_types_id'
            )
            ->pluck(
                'dlt.developed_land_types_desc_en',
                'dlt.developed_land_types_id'
            )
            ->toArray();
    }
    public static function getBasundharaLotNo(string $villageId): ?string
    {
        return self::where('village_id', $villageId)->value('basundhara_dharitri_lots_no');
    }
    public static function getBasundharaVillageCode(string $villageId): ?string
    {
        return self::where('village_id', $villageId)->value('basundhara_dharitri_village_code');
    }

    public static function getLandType($villageId){
        return self::where('village_id', $villageId)->value('developed_land_types_id');
    }
}
