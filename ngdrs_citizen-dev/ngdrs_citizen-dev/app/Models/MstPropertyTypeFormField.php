<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MstPropertyTypeFormField extends Model
{
    protected $table = 'ngdrstab_mst_property_type_form_fields';
    protected $guarded = [];

    public static function getByContext(int $propertyTypeId, int $articleId, int $areaTypeId)
    {
        $fields = DB::table('ngdrstab_mst_property_type_form_fields as ff')
            ->select(
                'ff.id',
                'ff.field_key',
                'ff.label',
                'ff.type',
                'ff.is_required',
                'ff.is_readonly',
                'ff.validation_rules',
                'ff.placeholder',
                'ff.options',
                'ff.field_actions',
                'section.key'      
            )
            ->join(
                'ngdrstab_mst_property_classification_form_field_mappings as maps',
                'ff.id',
                '=',
                'maps.form_field_id'
            )
            ->join(
                'ngdrstab_mst_property_classifications as context',
                'context.id',
                '=',
                'maps.classification_id'
            )
            ->join(
                'ngdrstab_mst_property_type_form_section as section',
                'section.id',
                '=',
                'maps.section_id'
            )
            ->where('context.property_type_id', $propertyTypeId)
            ->where('context.area_type_id', $areaTypeId)
            ->where('maps.display_flag', 'Y')
            ->orderBy('ff.display_order')        
            ->get();

        return $fields->groupBy('key')->toArray();
    }
}
