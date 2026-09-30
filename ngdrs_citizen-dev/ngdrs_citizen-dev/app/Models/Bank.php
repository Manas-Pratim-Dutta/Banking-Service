<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'ngdrstab_mst_bank';

    protected $primaryKey = 'bank_id';

    public $timestamps = false;
}