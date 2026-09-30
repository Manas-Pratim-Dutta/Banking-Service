<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyCategory extends Model
{
    protected $table = 'ngdrstab_mst_party_category';

    protected $primaryKey = 'category_id';

    public $timestamps = false;

    /**
     * Duplicate configuration
     */
    public static function get_duplicate($languagelist)
    {
        $duplicate = [];

        $duplicate['Table'] = 'ngdrstab_mst_party_category';

        $duplicate['PrimaryKey'] = 'category_id';

        $fields = [];

        foreach ($languagelist as $language) {

            $fields[] = 'category_name_' .
                $language['mainlanguage']['language_code'];
        }

        $duplicate['Fields'] = $fields;

        return $duplicate;
    }

    public static function get_party_category($doc_lang)
    {
        return self::where(
            'citizen_display_flag',
            'Y'
        )
            ->orderBy('category_id', 'ASC')
            ->pluck(
                'category_name_' . $doc_lang,
                'category_id'
            );
    }

    /**
     * Dynamic validation field list
     */
    public static function fieldlist($languagelist)
    {
        $fieldlist = [];

        foreach ($languagelist as $language) {

            $lang = $language['mainlanguage']['language_code'];

            if ($lang == 'en') {

                $fieldlist['category_name_' . $lang]['text']
                    = 'is_required,is_alphanumdashslash';

            } else {

                $fieldlist['category_name_' . $lang]['text']
                    = 'unicoderequired_rule_' . $lang;
            }
        }

        return $fieldlist;
    }
}