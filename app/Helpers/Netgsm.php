<?php

namespace App\Helpers;

class Netgsm
{
    public $username, $password, $client;
    public function __construct()
    {
        $this->username = getenv("NETGSM_USERNAME");
        $this->password = getenv("NETGSM_PASSWORD");
        $this->client = new \GuzzleHttp\Client();
    }

    public function SendSMS($phone, $message)
    {
        $params['form_params'] = array(
            'usercode' => $this->username,
            'password' => $this->password,
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $this->username
        );
        $url = 'https://api.netgsm.com.tr/sms/send/get/';
        $response = $this->client->post($url, $params);
        return $response;
    }
}
