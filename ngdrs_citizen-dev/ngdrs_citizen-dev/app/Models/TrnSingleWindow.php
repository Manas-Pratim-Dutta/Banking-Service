<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrnSingleWindow extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_trn_single_window';
    protected $primaryKey = 'id'; // change if your PK is different
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Save record from associative payload (like your $bb array)
     * Returns model instance on success.
     */
    public static function saveFromPayload(array $payload)
    {
        $payload['created'] = $payload['created'] ?? Carbon::now()->toDateTimeString();
        return static::create($payload);
    }

    public static function findByRtpsTransId($transId)
    {
        return static::where('rtps_trans_id', $transId)->first();
    }
}
