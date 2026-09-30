<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class TrnNocLandDetails extends Model
{
    protected $table = 'ngdrstab_trn_noc_landdetails';
    protected $guarded = [];

    public static function appointmentExists(string $nocNo)
    {
        return DB::table('ngdrstab_trn_noc_landdetails as noc')
            ->join('ngdrstab_trn_appointment_details as apt', 'apt.token_no', '=', 'noc.token_no')
            ->where('noc.appno', $nocNo)
            ->exists();
    }
}
