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


    public static function SendNotification($booking, $status): void
    {
        NotificationHelper::SendNotificationToDriver($booking, $status);
        NotificationHelper::SendNotificationToCustomer($booking, $status);
        //NotificationHelper::SendNotificationToAdmins($booking, $status);
        //NotificationHelper::SendNotificationToDriverManagers($booking, $status);
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

            self::SendNotification($booking, self::TRIP_IS_EXPECTED);
        }
    }

    public static function TripIsStarted($booking): void
    {
        //seyehat başladı, bildirm gönder
        $booking->update([
            'status' => self::TRIP_IS_STARTED
        ]);
        self::SendNotification($booking, self::TRIP_IS_STARTED);
    }

    public static function CanceledByCustomer($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_CUSTOMER
        ]);
        self::SendNotification($booking, self::CANCELED_BY_CUSTOMER);
    }

    public static function CanceledBySystem($booking): void
    {
        $booking->update([
            'status' => self::CANCELED_BY_SYSTEM
        ]);
        self::SendNotification($booking, self::CANCELED_BY_SYSTEM);
    }

    public static function TripIsCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_COMPLETED
        ]);

        self::SendNotification($booking, self::TRIP_IS_COMPLETED);
    }

    public static function TripIsNotCompleted($booking): void
    {
        $booking->update([
            'status' => self::TRIP_IS_NOT_COMPLETED
        ]);
        self::SendNotification($booking, self::TRIP_IS_NOT_COMPLETED);
    }

    public static function Completed($booking): void
    {
        $booking->update([
            'status' => self::COMPLETED
        ]);
        //muhasebe
        self::SendNotification($booking, self::COMPLETED);
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

            $full_discount = 0;
            $total = $booking_price;
            $discount_price = $total * (1.0 - (0 / 100.0));
            $driver_payment = $discount_price * 0.7;
            $system_payment = $discount_price - $driver_payment;
            $full_discount_price = $discount_price * (1.0 - ($full_discount / 100.0));

            $opening_fee = 0;

            $km_fee = $total / $booking->km;

            $inputs = [
                'booking_id' => $booking->id,
                'km' => $booking->km,
                'opening_fee' => $opening_fee,
                'km_fee' => $km_fee,
                'discount_rate' => 0,
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

            $booking->data->update($inputs);

            $booking->update([
                'driver_id' => $driver_id,
                'car_id' => $car_id,
                'car_type' => $car_type_id,
                'price_id' => 0,
                'status' => self::WAITING_FOR_BOOKING
            ]);

            self::SendNotification($booking, self::WAITING_FOR_BOOKING);
        }
    }
}
