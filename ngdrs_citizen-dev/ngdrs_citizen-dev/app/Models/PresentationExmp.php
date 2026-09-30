<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentationExmp extends Model
{
    protected $table = 'ngdrstab_mst_presentation_exemption';

    protected $primaryKey = 'exemption_id';

    public $timestamps = false;
}