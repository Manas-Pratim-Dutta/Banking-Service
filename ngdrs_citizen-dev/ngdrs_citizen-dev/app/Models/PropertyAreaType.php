<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAreaType extends Model
{
    protected $table = 'ngdrstab_mst_property_area_type';

    protected $primaryKey = 'area_type_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'area_type_desc_en',
        'flag',
        'old_area_type_id',
    ];

    protected $casts = [
        'area_type_id' => 'integer',
        'old_area_type_id' => 'integer',
    ];

    public static function getAreaTypeOptions(): array
    {
        return self::where('flag', 'Y')
            ->orderBy('area_type_id')
            ->pluck('area_type_desc_en', 'area_type_id')
            ->toArray();
    }

}
