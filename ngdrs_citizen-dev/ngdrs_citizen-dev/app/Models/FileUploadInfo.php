<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUploadInfo extends Model
{
    protected $table =
    'ngdrstab_trn_fileuploadinfo';


    protected $primaryKey =
    'up_id';


    public $timestamps = false;



    protected $fillable = [

        'token_no',

        'document_id',

        'input_fname',

        'out_fname',

        'state_id',

        'req_ip',

        'created',

        'user_id',

        'user_type',

        'org_user_id',

        'org_created',

        'org_updated'

    ];
}
