<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Netgsm;
use Exception;

class RegisterOtp extends Model
{
    protected $fillable = ['name', 'surname', 'email', 'password', 'phone', 'country_code', 'otp', 'expire_at'];


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendSMS($receiverNumber)
    {
        $message = "Register verification number is " . $this->otp;

        try {

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message
            ]);

            info('SMS Sent Successfully.');
        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
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
