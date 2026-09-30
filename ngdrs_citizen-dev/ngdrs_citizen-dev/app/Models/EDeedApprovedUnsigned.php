<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDeedApprovedUnsigned extends Model
{

    protected $table =
    'ngdrstab_trn_e_deed_approved_unsigned';


    protected $primaryKey='token_no';


    public $timestamps=false;


    protected $fillable=[
        'token_no',
        'file_path',
        'final_cert_check'
    ];

}