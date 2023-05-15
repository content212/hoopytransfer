<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'booking_time',
        'other_user_id'
    ];
    private const STATUS = [
        0 => 'Waiting for Booking',
        1 => 'Waiting for Confirmation',
        2 => 'Trip is expected',
        3 => 'Trip is started',
        4 => 'Canceled by Customer',
        5 => 'Canceled by System',
        6 => 'Trip is completed',
        7 => 'Trip is not Completed',
        8 => 'Completed',
        9 => 'Booking Request',
    ];
    private const PAYMENT_STATUS = [
        'requires_payment_method' => 'Not Paid',
        'succeeded' => 'Paid',
        'refund_pending' => 'Pending',
        'refund_succeeded' => 'Refunded',
        'refund_failed' => 'Failed',
        '' => null,
    ];
    protected $appends = ['status_name', 'payment_status'];
    public static function getCount($status)
    {
        $count = Booking::where('status', $status)->count();
        return self::STATUS[$status] . '(' . $count . ')';
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
        return self::STATUS;
    }
    public function getStatus(): string
    {
        return self::STATUS[$this->status];
    }
    public function getStatusNameAttribute($value)
    {
        return self::STATUS[$this->status];
    }
    public function getPaymentStatusAttribute($value)
    {
        return self::PAYMENT_STATUS[$this->payment?->status];
    }
    public function bookingDate() 
    {
        return Carbon::parse($this->status_date . ' ' .  $this->status_time);
    }
}
