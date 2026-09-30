<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{
    protected $table = 'ngdrstab_mst_salutation';

    protected $primaryKey = 'salutation_id';

    public $timestamps = false;
}