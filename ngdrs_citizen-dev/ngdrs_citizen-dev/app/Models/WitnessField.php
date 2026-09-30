<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WitnessField extends Model
{
    protected $table = 'ngdrstab_mst_witness_fields';

    protected $primaryKey = 'field_id';

    public $timestamps = false;

    public static function getFieldList(
        string $docLang = 'en',
        ?int $villageId = null
    ): array {

        $fieldList = [];

        $fields = self::where(
                'display_flag',
                'Y'
            )
            ->orderBy(
                'order',
                'ASC'
            )
            ->get();

        foreach ($fields as $field) {

            if ($field->is_list == 'N') {

                if (!empty($field->vrule_en)) {

                    $fieldList[
                        $field->field_id_name_en
                    ]['text']
                        = $field->vrule_en;
                }

                if (
                    $docLang === 'll' &&
                    !empty($field->vrule_ll)
                ) {

                    $fieldList[
                        $field->field_id_name_ll
                    ]['text']
                        = $field->vrule_ll;
                }

            } elseif (
                $field->is_list == 'Y'
            ) {

                if (!empty($field->vrule_en)) {

                    $fieldList[
                        $field->field_id_name_en
                    ]['select']
                        = $field->vrule_en;
                }
            }
        }

        return array_filter(
            $fieldList,
            fn ($key) => !empty($key),
            ARRAY_FILTER_USE_KEY
        );
    }
}