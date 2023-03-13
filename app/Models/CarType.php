<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CarType extends Model
{
    protected $table = 'car_types';

    protected $fillable = [
        'name',
        'image',
        'person_capacity',
        'baggage_capacity',
        'discount_rate',
        'free_cancellation'
    ];
    public function prices()
    {
        return $this->hasMany(Price::class, 'car_type', 'id');
    }
    public function imageUrl()
    {
        return $this->image
            ? Storage::disk('images')->url($this->image)
            : Storage::disk('images')->url('a.jpg');
    }
}
