<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverWorkList extends Model
{
    protected $fillable = ['user_id', 'date', 'start', 'finish', 'rest', 'detail', 'total'];
}
