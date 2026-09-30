<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeShift extends Model
{
    protected $table = 'ngdrstab_mst_officeshifttime';
    protected $primaryKey = 'shift_id';
    public $timestamps = false;
}
