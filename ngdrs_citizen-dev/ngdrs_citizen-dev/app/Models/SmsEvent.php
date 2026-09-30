<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsEvent extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_smsevent';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected function callSmsApi($templateId, $message, $phone)
    {
        // lightweight wrapper of your Cake curl logic
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://103.158.204.182/webservice/sendsms.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['content' => $message, 'mobileno' => $phone, 'templateid' => $templateId],
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) {
            \Log::error('SmsEvent::callSmsApi error: ' . $err);
            return false;
        }
        return json_decode($resp);
    }
    public function getEventById($eventId)
    {
        // Return SINGLE row, not collection
        return $this->where('event_id', $eventId)->first();
    }

    public function sendGenericSMS($templateId, $message, $phone)
    {
        $resp = $this->callSmsApi($templateId, $message, $phone);
        if ($resp === false) return false;
        if (isset($resp->error)) {
            \Log::error('SMS API error: ' . $resp->error);
            return false;
        }
        return true;
    }

    public function sendGenericSMS2($templateId, $message, $phone)
    {
        $resp = $this->callSmsApi($templateId, $message, $phone);
        return $resp->data ?? null;
    }
}
