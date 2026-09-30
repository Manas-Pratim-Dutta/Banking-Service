<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstPropertyTypeClassifications extends Model
{
    protected $table = 'ngdrstab_mst_property_classifications';
    protected $guarded = [];

    public static function classficationExists($areaTypeId, $propertyTypeId, $articleId)
    {
        return self::where('area_type_id', $areaTypeId)->where('property_type_id', $propertyTypeId)->exists();
    }

    public static function getShowLocationDetailFlag($areaTypeId, $propertyTypeId, $articleId)
    {
        return self::where('area_type_id', $areaTypeId)->where('property_type_id', $propertyTypeId)->value('show_location_details');
    }
    public static function getshowDagPattaContainerFlag($areaTypeId, $propertyTypeId, $articleId)
    {
        return self::where('area_type_id', $areaTypeId)->where('property_type_id', $propertyTypeId)->value('show_dag_patta_div');
    }

    public static function getClassification($areaTypeId, $propertyTypeId, $articleId)
    {
        return self::where('area_type_id', $areaTypeId)->where('property_type_id', $propertyTypeId)->first();
    }

}
