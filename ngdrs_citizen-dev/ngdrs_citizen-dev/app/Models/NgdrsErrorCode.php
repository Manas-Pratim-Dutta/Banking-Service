<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NgdrsErrorCode extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_errorcodes';
    protected $primaryKey = 'error_code_id';
    public $timestamps = false;
    protected $guarded = [];

    public static function getAll()
    {
        return static::orderBy('error_code_id', 'asc')->get();
    }
}
