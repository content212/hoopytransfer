<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lager extends Model
{
    protected $fillable = [
        'name',
        'isOvertime',
        'overtime'
    ];
}
