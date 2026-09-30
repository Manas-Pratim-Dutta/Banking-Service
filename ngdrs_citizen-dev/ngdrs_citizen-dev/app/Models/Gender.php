<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'ngdrstab_mst_gender';

    protected $primaryKey = 'gender_id';

    public $timestamps = false;
}