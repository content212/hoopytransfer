<?php

namespace App\Helpers;

use App\Models\BookingService;
use App\Models\CarType;
use App\Models\Price;
use App\Models\Setting;

class BookingHelper
{

    public const WAITING_FOR_BOOKING = 0;
    private const WAITING_FOR_CONFIRMATION = 1;
    private const TRIP_IS_EXPECTED = 2;
    private const TRIP_IS_STARTED = 3;
    private const CANCELED_BY_CUSTOMER = 4;
    private const CANCELED_BY_SYSTEM = 5;
    private const TRIP_IS_COMPLETED = 6;
    private const TRIP_IS_NOT_COMPLETED = 7;
    private const COMPLETED = 8;
    private const BOOKING_REQUEST = 9;

    public static function SetBookingStatus($booking, $car_type, $car_id, $driver_id, $price, $status): void
    {
        if (!$booking) {
            throw new Exception('Booking not found.');
        }

        switch ($status) {
            case self::WAITING_FOR_BOOKING:
                break;
            case self::WAITING_FOR_CONFIRMATION:
                self::WaitingForConfirmation($booking, $driver_id, $car_id, $car_type);
                break;
            case self::TRIP_IS_EXPECTED:
                self::TripIsExpected($booking, $driver_id, $car_id, $car_type);
                break;
            case self::TRIP_IS_STARTED:
                self::TripIsStarted($booking);
                break;
            case self::CANCELED_BY_CUSTOMER:
                self::CanceledByCustomer($booking);
                break;
            case self::CANCELED_BY_SYSTEM:
                self::CanceledBySystem($booking);
                break;
            case self::TRIP_IS_COMPLETED:
                self::TripIsCompleted($booking);
                break;
            case self::TRIP_IS_NOT_COMPLETED:
                self::TripIsNotCompleted($booking);
                break;
            case self::COMPLETED:
                self::Completed($booking);
                break;
            case self::BOOKING_REQUEST:
                self::BookingRequest($booking, $driver_id, $car_id, $car_type, $price);
                break;
        }

    }


    public static function SendNotification($booking, $title, $body): void
    {
        NotificationHelper::SendNotificationToDriver($booking->driver_id, $title, $body, $booking);
        NotificationHelper::SendNotificationToUser($booking->user_id, $title, $body, $booking);
        NotificationHelper::SendNotificationToAdmins($title, $body, $booking);
    }

    public static function WaitingForConfirmation($booking, $driver_id, $car_id, $car_type): void
    {
        //şöföre ata
        //booking i trip is expected yap
        //bildirim sms mail
        if ($driver_id > 0 && $car_id > 0 && $car_type > 0) {
            $booking->update([
                'driver_id' => $driver_id,
                'car_id' => $car_id,
                'car_type' => $car_type,
            ]);
            self::TripIsExpected($booking, $driver_id, $car_id, $car_type);
        }
    }

    public static function TripIsExpected($booking, $driver_id, $car_id, $car_type): void
    {
        //şöför değişikliğini yap
        //bildirim sms mail

        if ($driver_id > 0 && $car_id > 0 && $car_type > 0) {
            $booking->update([
                'driver_id' => $driver_id,
                'car_id' => $car_id,
                'car_type' => $car_type,
                'status' => self::TRIP_IS_EXPECTED
            ]);

            $title = 'Trip Is Expected';
            $body = 'Trip Is Expected ' . $booking->track_code;
            self::SendNotification($booking, $title, $body);
        }
    }

    public static function TripIsStarted($booking): void
    {
        //seyehat başladı, bildirm gönder
        $booking->update([
            'status' => self::TRIP_IS_STARTED
        ]);

        $title = 'Trip Is Started';
        $body = 'Trip Is Started ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function CanceledByCustomer($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_CUSTOMER
        ]);

        $title = 'Canceled By Customer';
        $body = 'Canceled By Customer ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function CanceledBySystem($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_SYSTEM
        ]);

        $title = 'Canceled By System';
        $body = 'Canceled By System ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function TripIsCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_COMPLETED
        ]);

        $title = 'Trip Is Completed';
        $body = 'Trip Is Completed ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function TripIsNotCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_NOT_COMPLETED
        ]);

        $title = 'Trip Is Not Completed';
        $body = 'Trip Is Not Completed ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function Completed($booking): void
    {
        $booking->update([
            'status' => self::COMPLETED
        ]);
        //muhasebe

        $title = 'Completed';
        $body = 'Completed ' . $booking->track_code;
        self::SendNotification($booking, $title, $body);
    }

    public static function BookingRequest($booking, $driver_id, $car_id, $car_type_id, $booking_price): void
    {
        //fiyat kaydet
        //driver kaydet
        //durumu waiting for booking yap (0)

        if ($driver_id > 0 && $car_id > 0 && $car_type_id > 0 && $booking_price > 0) {

            $car_type = CarType::where('id', '=', $car_type_id)->first();

            $bookingService = BookingService::where('booking_id', '=', $booking->id)->first();

            if ($bookingService) {
                $bookingService->update(array(
                    'name' => $car_type->name,
                    'image' => $car_type->image,
                    'person_capacity' => $car_type->person_capacity,
                    'baggage_capacity' => $car_type->baggage_capacity,
                    'free_cancellation' => $car_type->free_cancellation
                ));
            } else {
                BookingService::create(array(
                    'booking_id' => $booking->id,
                    'name' => $car_type->name,
                    'image' => $car_type->image,
                    'person_capacity' => $car_type->person_capacity,
                    'baggage_capacity' => $car_type->baggage_capacity,
                    'free_cancellation' => $car_type->free_cancellation
                ));
            }

            $price = Price::where('car_type', '=', $car_type_id)->orderBy('km_fee', 'desc')->first();
            $full_discount = Setting::firstWhere('code', 'full_discount')->value ?? 0;
            $total = $booking_price;
            $discount_price = $total * (1.0 - ($price->carType->discount_rate / 100.0));
            $driver_payment = $discount_price * 0.7;
            $system_payment = $discount_price - $driver_payment;
            $full_discount_price = $discount_price * (1.0 - ($full_discount / 100.0));
            $inputs = [
                'booking_id' => $booking->id,
                'km' => $booking->km,
                'opening_fee' => $price->opening_fee,
                'km_fee' => $price->km_fee,
                'discount_rate' => $price->carType->discount_rate,
                'discount_price' => $discount_price,
                'system_payment' => $system_payment,
                'driver_payment' => $driver_payment,
                'total' => $total,
                'payment_type' => 'Full',
                'full_discount' => $full_discount,
                'full_discount_price' => $full_discount_price,
                'full_discount_system_payment' => $system_payment - (($discount_price * ($full_discount / 100.0)) * 0.3),
                'full_discount_driver_payment' => $driver_payment - (($discount_price * ($full_discount / 100.0)) * 0.7)
            ];

            if ($price) {

                $booking->data->update($inputs);

                $booking->update([
                    'driver_id' => $driver_id,
                    'car_id' => $car_id,
                    'car_type' => $car_type_id,
                    'price_id' => $price->id,
                    'status' => self::WAITING_FOR_BOOKING
                ]);

                $title = 'Price Offer Has Been Completed';
                $body = 'Price Offer Has Been Completed ' . $booking->track_code;
                self::SendNotification($booking, $title, $body);
            }
        }
    }
}
