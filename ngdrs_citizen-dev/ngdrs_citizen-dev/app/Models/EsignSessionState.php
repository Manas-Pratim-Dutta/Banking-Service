<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsignSessionState extends Model
{
    protected $table = 'esign_session_states';

    protected $fillable = [
        'token_no',
        'user_id',
        'session_data',
    ];
}