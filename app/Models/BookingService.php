<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BookingService extends Model
{
    protected $table = 'booking_services';

    protected $fillable = [
        'booking_id',
        'name',
        'image',
        'person_capacity',
        'baggage_capacity',
        'free_cancellation'
    ];
    protected $appends = ['image_url'];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'id', 'booking_id');
    }
    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('images')->url($this->image)
            : Storage::disk('images')->url('a.jpg');
    }
    public function imageUrl()
    {
        return $this->image
            ? Storage::disk('images')->url($this->image)
            : Storage::disk('images')->url('a.jpg');
    }
}
