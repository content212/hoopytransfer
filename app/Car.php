<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    protected $fillable = ['name', 'type', 'person_capacity', 'baggage_capacity'];
}
