<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faith extends Model
{
    protected $table = 'ngdrstab_mst_faith';

    protected $primaryKey = 'faith_id';

    public $timestamps = false;
}