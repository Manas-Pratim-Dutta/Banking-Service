<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentifierField extends Model
{
    protected $table = 'ngdrstab_mst_identifire_fields';

    protected $primaryKey = 'field_id';

    public $timestamps = false;
}