<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstUsageItemsList extends Model
{
    protected $table = "ngdrstab_mst_usage_items_list";

    public static function getItems()
    {
        return self::select('usage_param_id', 'field_key')
            ->whereNotNull('field_key')
            ->get();
    }
}
