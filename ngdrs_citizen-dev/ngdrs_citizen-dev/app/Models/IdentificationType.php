<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentificationType extends Model
{
    protected $table = 'ngdrstab_mst_identificationtype';

    protected $primaryKey = 'identificationtype_id';

    public $timestamps = false;
}