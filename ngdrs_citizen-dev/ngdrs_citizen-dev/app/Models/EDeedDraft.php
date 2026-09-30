<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDeedDraft extends Model
{
    protected $table = 'ngdrstab_trn_e_deed_draft';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'token_no',
        'file_path',
        'language_type',
        'esign_status_presenter',
        'esign_status_presenter_data',
        'session_data',
    ];

    protected $casts = [
        'esign_status_presenter_data' => 'array',
        'session_data' => 'array',
    ];
}