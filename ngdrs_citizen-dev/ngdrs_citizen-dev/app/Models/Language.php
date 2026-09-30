<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_conf_language';
    protected $primaryKey = 'language_id';
    public $timestamps = false;
    protected $guarded = [];

    public static function getAllLanguages()
    {
        return static::orderBy('language_name')->get();
    }

    public static function getLanguageForState($stateId)
    {
        return static::where('state_id', $stateId)->first();
    }
}
