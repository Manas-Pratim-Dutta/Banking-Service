<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $table = "ngdrstab_mst_property_type";

    protected $primaryKey = "property_type_id";
    protected $guarded = [];

    public static function getPropertyTypeOptions()
    {
        return self::where('flag', 'Y')
            ->orderBy('property_type_id')
            ->pluck('property_type_desc_en', 'property_type_id')
            ->toArray();
    }
}
