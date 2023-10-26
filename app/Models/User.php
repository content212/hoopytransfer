<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function isDeleted()
    {
        return $this->deleted_at != null;
    }
    public function sendNotification($title, $body, $booking)
    {
        $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY');
        $devices = DB::table('user_devices')->select('id', 'device_token')->where('user_id', $this->id)->get();
        foreach ($devices as $device) {

            $data = [
                "to" => $device->device_token,
                "data" => [
                    "bookingId" => $booking->id,
                ],
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ]
            ];

            $dataString = json_encode($data);
            echo $dataString;
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'phone', 'status', 'country_code', 'deleted_at', 'delete_reason'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
