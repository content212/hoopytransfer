<?php

namespace App\Helpers;

use App\Models\BookingService;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class NotificationHelper
{
    public static function SendNotificationToDriver($booking, $status): void
    {
        $driver = Driver::where('id', '=', $booking->driver_id)->first();
        if ($driver) {
            $driver_user = User::where('id', '=', $driver->user_id)->first();
            if ($driver_user) {
                $notification = Notification::where('role', 'driver')
                    ->where('status', $status)
                    ->first();
                if ($notification) {
                    self::Send($notification, $driver_user, $booking);
                }
            }
        }
    }


    public static function SendNotificationToCustomer($booking, $status): void
    {
        $user = User::where('id', '=', $booking->user_id)->first();
        if ($user) {
            $notification = Notification::where('role', 'customer')
                ->where('status', $status)
                ->first();

            if ($notification) {
                self::Send($notification, $user, $booking);
            }
        }
    }

    public static function SendNotificationToAdmins($booking, $status): void
    {
        $admin_users = Role::where('role', '=', 'admin')->get();
        foreach ($admin_users as $admin_user) {
            $adminUser = User::where('id', '=', $admin_user->user_id)->first();
            if ($adminUser) {
                $notification = Notification::where('role', 'admin')
                    ->where('status', $status)
                    ->first();
                if ($notification) {
                    self::Send($notification, $adminUser, $booking);
                }
            }
        }
    }

    public static function SendNotificationToDriverManagers($booking, $status): void
    {
        $drivermanager_users = Role::where('role', '=', 'driver_manager')->get();
        foreach ($drivermanager_users as $drivermanager_user) {
            $driverManagerUser = User::where('id', '=', $drivermanager_user->user_id)->first();
            if ($driverManagerUser) {
                $notification = Notification::where('role', 'driver_manager')
                    ->where('status', $status)
                    ->first();
                if ($notification) {
                    self::Send($notification, $driverManagerUser, $booking);
                }
            }
        }
    }


    public static function Send($notification, $user, $booking)
    {
        if ($user && $booking) {
            if ($notification->push_enabled && $notification->push_title && $notification->push_body) {
                self::SendFirebaseNotification($user, $booking, self::Replace($notification->push_title, $booking), self::Replace($notification->push_body, $booking));
            }

            if ($notification->sms_enabled && $notification->sms_body) {
                self::SendSms($user->country_code . $user->phone, self::Replace($notification->sms_body, $booking));
            }

            if ($notification->email_enabled && $notification->email_subject && $notification->email_body) {
                self::SendEmail($user, self::Replace($notification->email_subject, $booking), self::Replace($notification->email_body, $booking));
            }
        }
    }

    public static function Replace($text, $booking)
    {
        $booking_date_time = $booking->booking_date . ' ' . $booking->booking_time;
        $text = str_replace("{reservation_no}", $booking->track_code, $text);
        $bookingService = BookingService::where('booking_id',$booking->id)->first();
        if ($bookingService && $bookingService->free_cancellation) {
            $text = str_replace("{cancellation_time}", $bookingService->free_cancellation, $text);
        } else {
            $text = str_replace("{cancellation_time}", "", $text);
        }
        return str_replace("{booking_date_time}", $booking_date_time, $text);
    }



    public static function SendSms($receiverNumber, $message)
    {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_TOKEN");
        $twilio_number = env("TWILIO_FROM");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/' . $account_sid . '/Messages.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $account_sid . ':' . $auth_token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'To=' . $receiverNumber . '&From=' . $twilio_number . '&Body=' . $message);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    public static function SendEmail($user, $subject, $mail)
    {

        $SENDGRID_API_KEY = env('SENDGRID_API_KEY');
        $SENDGRID_TEMPLATE_ID = env('SENDGRID_TEMPLATE_ID');
        $SENDGRID_SENDER = env('SENDGRID_SENDER');

        $headers = [
            'Authorization: Bearer ' . $SENDGRID_API_KEY,
            'Content-Type: application/json',
        ];

        $data = '
        {
           "from":{
              "email":"' . $SENDGRID_SENDER . '"
           },
           "personalizations":[
              {
                 "to":[
                    {
                       "email":"' . $user->email . '"
                    }
                 ],
                 "dynamic_template_data":{
                    "subject": "' . $subject . '",
                    "icerik":"' . $mail . '",
                  }
              }
           ],
           "template_id":"' . $SENDGRID_TEMPLATE_ID . '"
        }
        ';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_exec($ch);

    }

    public static function SendFirebaseNotification($user, $booking, $title, $body)
    {
        $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY');
        $devices = DB::table('user_devices')->select('id', 'device_token')->where('user_id', $user->id)->get();
        $role = Role::where('user_id', '=', $user->id)->first();

        $roleName = "customer";
        if ($role && $role->role) {
            $roleName = $role->role;
        }

        foreach ($devices as $device) {
            $data = [
                "to" => $device->device_token,
                "data" => [
                    "bookingId" => $booking->id,
                    "role" => $roleName,
                ],
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ]
            ];

            $dataString = json_encode($data);
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

            curl_exec($ch);
        }
    }
}
