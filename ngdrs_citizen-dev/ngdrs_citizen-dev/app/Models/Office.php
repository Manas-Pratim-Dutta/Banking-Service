<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table      = 'ngdrstab_mst_office';
    protected $primaryKey = 'office_id';
    public    $timestamps = false;

    public function shift()
    {
        return $this->belongsTo(OfficeShift::class, 'shift_id', 'shift_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(TimeSlot::class, 'slot_id', 'slot_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'office_id', 'office_id');
    }
}
