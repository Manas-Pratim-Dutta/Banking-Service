<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identifier extends Model
{
    protected $table = 'ngdrstab_trn_identification';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];
}