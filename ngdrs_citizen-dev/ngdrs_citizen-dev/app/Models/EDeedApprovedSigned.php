<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EDeedApprovedSigned extends Model
{

    protected $table =
    'ngdrstab_trn_e_deed_approved_signed';


    protected $primaryKey='token_no';


    public $timestamps=false;


    protected $fillable=[

        'token_no',
        'file_path',
        'esign_status',
        'esign_check',
        'esign_data',
        'session_data'

    ];

}