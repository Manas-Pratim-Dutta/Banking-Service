<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Witness extends Model
{
    /**
     * Table name (same as CakePHP)
     */
    protected $table = 'ngdrstab_trn_witness';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * Auto incrementing
     */
    public $incrementing = true;

    /**
     * Timestamp handling (CakePHP tables usually don't have Laravel timestamps)
     */
    public $timestamps = false;

    /**
     * Mass assignable fields
     */
    protected $fillable = [

        'token_no',
        'state_id',
        'user_id',
        'user_type',

        'salutation',
        'witness_full_name_en',
        'father_full_name_en',

        'occupation_id',
        'gender_id',
        'age',

        'mobile_no',
        'pan_no',
        'email_id',

        'address_en',
        'pin_code',

        'district_id',
        'taluka_id',
        'village_id',

        'homevisit_flag',

        'identificationtype_id',
        'identificationtype_desc_en',

        'auth_data',
        'ekyc_trans_no',

        'req_ip',

        'dob'
    ];
}