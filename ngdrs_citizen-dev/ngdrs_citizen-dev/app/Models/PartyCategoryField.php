<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyCategoryField extends Model
{
    protected $table = 'ngdrstab_mst_party_category_fields';

    protected $primaryKey = 'field_id';

    public $timestamps = false;

    public static function get_party_fields(
        $categoryId,
        $articleId
    ) {

        return self::where(
            'category_id',
            $categoryId
        )
            ->where(
                'display_flag',
                'Y'
            )
            // ->where(
            //     'is_auth_signtry_field',
            //     'N'
            // )
            ->whereIn(
                'article_id',
                [9999, $articleId]
            )
            ->orderBy('order')
            ->get();
    }

}
