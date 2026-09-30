<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfListItems extends Model
{
    protected $table = 'ngdrstab_conf_list_items';
    protected $guarded = [];

    public static function getItems(){
        return self::select('id', 'item_desc_en')->get();
    }
}
