<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfDistrict extends Model
{
    protected $table = 'ngdrstab_conf_admblock3_district';

    protected $primaryKey = 'district_id';
    protected $guarded = [];

    public static function getAllDistrict(): array
    {
        return self::orderBy('district_name_en')
            ->pluck('district_name_en', 'district_id')
            ->toArray();
    }

    public static function getDistrictId(string $dharitriDistrictCode): ?int
    {
        return self::where('basundhara_dharitri_code', $dharitriDistrictCode)->value('district_id');
    }

    public static function getDistrictDetails(string $dharitriDistrictCode): ?object
    {
        return self::where(
            'basundhara_dharitri_code',
            $dharitriDistrictCode
        )->select('district_id', 'district_name_en')->first();
    }

    public static function getDistrictByBasundharaCode(string $districtCode): ?object
    {
        return self::where(
            'basundhara_dharitri_code',
            $districtCode
        )
            ->select(
                'district_id',
                'district_name_en',
                'basundhara_dharitri_code'
            )
            ->first();
    }

    public static function getBasundharaDistrictCode(string $districtId): ?string
    {
        return self::where('district_id', $districtId)
            ->value('basundhara_dharitri_code');
    }
}
