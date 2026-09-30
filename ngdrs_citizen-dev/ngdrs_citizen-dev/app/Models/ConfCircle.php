<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfCircle extends Model
{
    //Circle is mouza
    protected $table = 'ngdrstab_conf_admblock6_circle';

    public static function getCircleId(string $districtId, string $subDivId, string $talukaId, string $dharitriCircleCode): ?int
    {
        return self::where('district_id', $districtId)->where('subdivision_id', $subDivId)->where('taluka_id', $talukaId)
            ->where('basundhara_dharitri_mouza_code', $dharitriCircleCode)->value('circle_id');
    }

    public static function getCircleDetails(
        string $districtId,
        string $subDivId,
        string $talukaId,
        string $dharitriCircleCode
    ): ?object {
        return self::where('district_id', $districtId)
            ->where('subdivision_id', $subDivId)
            ->where('taluka_id', $talukaId)
            ->where(
                'basundhara_dharitri_mouza_code',
                $dharitriCircleCode
            )
            ->select('circle_id', 'circle_name_en')
            ->first();
    }

    public static function getMouja($districtId, $subdivId, $circleId)
    {
        return self::where('district_id', $districtId)->where('subdivision_id', $subdivId)->where('taluka_id', $circleId)->pluck('circle_name_en', 'circle_id')->toArray();
    }

    public static function getBasundharaMoujaCode(string $moujaId): ?string
    {
        return self::where('circle_id', $moujaId)->value('basundhara_dharitri_mouza_code');
    }
}
