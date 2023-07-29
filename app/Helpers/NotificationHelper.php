<?php

namespace App\Helpers;
use App\Models\User;
use App\Models\Booking;
use App\Models\Setting;


class NotificationHelper
{
    public static function SendFirebaseNotification(User $user, $bookingStatusId): void
    {
        $title = Setting::where('code','booking_status_firebase_title_' . $bookingStatusId)->first();
        $body = Setting::where('code','booking_status_firebase_body_' . $bookingStatusId)->first();
        if (!title && !$body) {
            $user->sendNotification($title->value,$body->value);
        }
    }
}
