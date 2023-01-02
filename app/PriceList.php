<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'company_id',
        'is_active',
        '1_weekday',
        '2_weekday',
        '3_weekday',
        '4_weekday',
        '1_saturday',
        '2_saturday',
        '3_saturday',
        '4_saturday',
        '1_sunday',
        '2_sunday',
        '3_sunday',
        '4_sunday',
        'start_date',
        'end_date'
    ];
}
