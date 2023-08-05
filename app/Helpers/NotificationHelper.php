<?php

namespace App\Helpers;

use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use App\Models\Setting;


class NotificationHelper
{
    public static function SendNotificationToDriver($driver_id, $title, $body, $booking): void
    {
        $driver = Driver::where('id', '=', $driver_id)->first();
        if ($driver) {
            $driver_user = User::where('id', '=', $driver->user_id)->first();
            if ($driver_user) {
                $driver_user->sendNotification($title, $body, $booking);
            }
        }
    }

    public static function SendNotificationToUser($user_id, $title, $body, $booking): void
    {
        $user = User::where('id', '=', $user_id)->first();
        if ($user) {
            $user->sendNotification($title, $body, $booking);
        }
    }

    public static function SendNotificationToAdmins($title, $body, $booking): void
    {
        $admin_users = Role::where('role', '=', 'admin')->get();
        foreach ($admin_users as $admin_user) {
            $adminUser = User::where('id', '=', $admin_user->user_id)->first();
            if ($adminUser) {
                $adminUser->sendNotification($title, $body, $booking);
            }
        }
    }
}
