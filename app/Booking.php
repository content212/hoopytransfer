<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['from', 'km', 'duration', 'from_name', 'from_address', 'from_lat', 'from_lng', 'to', 'to_name', 'to_address', 'to_lat', 'to_lng', 'status'];
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
