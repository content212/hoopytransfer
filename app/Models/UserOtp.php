<?php

namespace App\Models;

use App\Helpers\Netgsm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Twilio\Rest\Client;

class UserOtp extends Model
{

    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = ['user_id', 'otp', 'expire_at'];

    /**
     * Write code on Method
     *
     * @return string()
     */
    public function sendSMS($receiverNumber)
    {
        $message = "Login verification number is " . $this->otp;
        try {

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $message = $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message
            ]);
            info('SMS Sent Successfully.');
        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
            return $e->getMessage();
        }
        //try {
        //
        //    $client = new Netgsm();
        //    $response = $client->sendSMS($receiverNumber, $message);
        //    error_log($message);
        //
        //} catch (Exception $e) {
        //    info("Error: " . $e->getMessage());
        //}
    }
}
