<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        'name',
        'official_name',
        'official_phone',
        'country',
        'state',
        'address',
        'latitude',
        'longitude'
    ];
}
