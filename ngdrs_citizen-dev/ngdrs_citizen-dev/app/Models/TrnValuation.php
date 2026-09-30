<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrnValuation extends Model
{
    protected $table = "ngdrstab_trn_valuation";
    protected $primaryKey = 'val_id';
    protected $guarded = [];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
