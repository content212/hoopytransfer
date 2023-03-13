<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Netgsm;
use Exception;

class RegisterOtp extends Model
{
    protected $fillable = ['name', 'surname', 'email', 'password', 'phone', 'otp', 'expire_at'];


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendSMS($receiverNumber)
    {
        $message = "Register verification number is " . $this->otp;

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
