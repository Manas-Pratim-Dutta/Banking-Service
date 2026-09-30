<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfSubdivision extends Model
{
    protected $table = 'ngdrstab_conf_admblock4_subdivision';
    protected $guarded = [];
    protected $primaryKey = 'subdivision_id';

    public static function getAllSubdivisions($districtId): array
    {
        return self::where('district_id', $districtId)->orderBy('subdivision_name_en')->pluck('subdivision_name_en', 'subdivision_id')->toArray();
    }

    public static function getSubDivId(string $districtId, string $dharitriSubDivCode): ?int
    {
        return self::where('district_id', $districtId)->where('basundhara_dharitri_subdiv_code', $dharitriSubDivCode)->value('subdivision_id');
    }

    public static function getSubDivisionDetails(
        string $districtId,
        string $dharitriSubDivCode
    ): ?object {
        return self::where('district_id', $districtId)
            ->where(
                'basundhara_dharitri_subdiv_code',
                $dharitriSubDivCode
            )
            ->select('subdivision_id', 'subdivision_name_en')
            ->first();
    }

    public static function getBasundharaSubdivCode(string $subdivId): ?string {
        return self::where('subdivision_id', $subdivId)->value('basundhara_dharitri_subdiv_code');
    }
}
