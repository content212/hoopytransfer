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
     * @return response()
     */
    public function sendSMS($receiverNumber)
    {
        $message = "Login verification number is " . $this->otp;

        try {

            $client = new Netgsm();
            $response = $client->sendSMS($receiverNumber, $message);
            error_log($message);
            info('SMS Sent Successfully.');
        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
        }
    }
}
