<?php

namespace App\Helpers;

use App\Models\Price;
use App\Models\UserContract;
use App\Models\Booking;
use App\Models\BookingData;
use Illuminate\Support\Str;

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
                'status' => self::TRIP_IS_EXPECTED
            ]);
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
            ]);
        }
    }

    public static function TripIsStarted($booking)
    {
        //seyehat başladı, bildirm gönder
        $booking->update([
            'status' => self::TRIP_IS_STARTED
        ]);
    }

    public static function CanceledByCustomer($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_CUSTOMER
        ]);
    }

    public static function CanceledBySystem($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_SYSTEM
        ]);
    }

    public static function TripIsCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_COMPLETED
        ]);
    }

    public static function TripIsNotCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_NOT_COMPLETED
        ]);
    }

    public static function Completed($booking): void
    {
        $booking->update([
            'status' => self::COMPLETED
        ]);
        //muhasebe
    }

    public static function BookingRequest($booking, $driver_id, $car_id, $car_type, $booking_price): void
    {
        //fiyat kaydet
        //driver kaydet
        //durumu waiting for booking yap (0)

        if ($driver_id > 0 && $car_id > 0 && $car_type > 0 && $booking_price > 0) {
            $price = Price::where('car_type', '=', $car_type) . orderBy('km_fee', 'desc')->first();
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
                    'car_type' => $car_type,
                    'price_id' => $price->id,
                    'status' => self::WAITING_FOR_BOOKING
                ]);

            }
        }
    }
}
