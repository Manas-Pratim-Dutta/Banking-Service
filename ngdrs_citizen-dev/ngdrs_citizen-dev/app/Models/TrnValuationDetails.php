<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrnValuationDetails extends Model
{
    protected $table = "ngdrstab_trn_valuation_details";
    protected $guarded = [];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
