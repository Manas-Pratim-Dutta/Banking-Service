<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstFinyear extends Model
{
    protected $table = "ngdrstab_mst_finyear";

    public static function getFinYear(){
        return self::where('current_year', 'Y')->value('finyear_id');
    }
}
