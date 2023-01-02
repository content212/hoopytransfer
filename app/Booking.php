<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['from', 'km', 'duration', 'from_name', 'from_address', 'from_lat', 'from_lng', 'to', 'to_name', 'to_address', 'to_lat', 'to_lng', 'status', 'delivery_type', 'delivery_date', 'delivery_time', 'sender_name', 'sender_phone', 'sender_mail', 'customer_name', 'customer_phone', 'customer_mail', 'company_name', 'user_id', 'user_type'];
    public static function getCount($status)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $_COOKIE['token'],
            ];
            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);
            $response = $client->get(env("APP_URL", 'http://localhost') . '/api/bookingscount/' . $status);
            $count = $response->getBody();
            $count = str_replace(' ', '', $count);
            return $count;
        } catch (\Exception $exception) {
            return 0;
        }
    }
}
