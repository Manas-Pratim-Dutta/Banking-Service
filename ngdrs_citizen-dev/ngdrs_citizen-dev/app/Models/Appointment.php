<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class Appointment extends Model
// {
//     protected $table = 'ngdrstab_trn_appointment_details';

//     public $timestamps = false;
//     protected $primaryKey = 'id';
//     protected $dates = ['appointment_date'];

//     public function office()
//     {
//         return $this->belongsTo(Office::class, 'office_id', 'office_id');
//     }
//     protected $fillable = ['office_id', 'appointment_date', 'sheduled_time', 'user_id', 'req_ip', 'created_at'];
// }


class Appointment extends Model
{
    protected $table = 'ngdrstab_trn_appointment_details';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'office_id',
        'shift_id',
        'appointment_date',
        'sheduled_time',
        'interval_id',
        'slot_no',
        'token_no',
        'user_type',
        'flag',
        'user_id',
        'req_ip',
        'created'
    ];

    protected $casts = [
        'appointment_date' => 'date'
    ];

    public function office()
    {
        return $this->belongsTo(
            Office::class,
            'office_id',
            'office_id'
        );
    }
}
