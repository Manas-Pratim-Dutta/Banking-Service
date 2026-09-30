<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $table = 'ngdrstab_mst_timeslot';
    protected $primaryKey = 'slot_id';
    public $timestamps = false;
}
