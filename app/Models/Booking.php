<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'status',
        'user_id',
        'track_code',
        'driver_id',
        'car_id',
        'car_type',
        'price_id',
        'km',
        'duration',
        'from',
        'from_name',
        'from_address',
        'from_lat',
        'from_lng',
        'to',
        'to_name',
        'to_address',
        'to_lat',
        'to_lng',
        'booking_date',
        'booking_time'
    ];
    protected $appends = ['status_name'];
    public static function getCount($status)
    {
        $count = Booking::where('status', $status)->count();
        return [
            0 => 'Waiting for Booking',
            1 => 'Waiting for Confirmation',
            2 => 'Trip is expected',
            3 => 'Canceled by Customer',
            4 => 'Canceled by System',
            5 => 'Trip is completed',
            6 => 'Trip is not Completed',
            7 => 'Completed',
            8 => 'Booking Request'
        ][$status] . '(' . $count . ')';
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function data()
    {
        return $this->hasOne(BookingData::class, 'booking_id', 'id');
    }
    public function payment()
    {
        return $this->hasOne(BookingPayment::class, 'booking_id', 'id');
    }
    public function service()
    {
        return $this->hasOne(CarType::class, 'id', 'car_type');
    }
    public static function getAllStatus(): array
    {
        return [
            0 => 'Waiting for Booking',
            1 => 'Waiting for Confirmation',
            2 => 'Trip is expected',
            3 => 'Canceled by Customer',
            4 => 'Canceled by System',
            5 => 'Trip is completed',
            6 => 'Trip is not Completed',
            7 => 'Completed',
            8 => 'Booking Request'
        ];
    }
    public function getStatus(): string
    {
        return [
            0 => 'Waiting for Booking',
            1 => 'Waiting for Confirmation',
            2 => 'Trip is expected',
            3 => 'Canceled by Customer',
            4 => 'Canceled by System',
            5 => 'Trip is completed',
            6 => 'Trip is not Completed',
            7 => 'Completed',
            8 => 'Booking Request'
        ][$this->status];
    }
    public function getStatusNameAttribute($value)
    {
        return [
            0 => 'Waiting for Booking',
            1 => 'Waiting for Confirmation',
            2 => 'Trip is expected',
            3 => 'Canceled by Customer',
            4 => 'Canceled by System',
            5 => 'Trip is completed',
            6 => 'Trip is not Completed',
            7 => 'Completed',
            8 => 'Booking Request'
        ][$this->status];
    }
}
