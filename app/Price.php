<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'area',
        'zip_code',
        'bp_km_price',
        'bp_small_6',
        'bp_small_3',
        'bp_small_2',
        'bp_small_express',
        'bp_small_timed',
        'bp_medium_6',
        'bp_medium_3',
        'bp_medium_2',
        'bp_medium_express',
        'bp_medium_timed',
        'bp_large_6',
        'bp_large_3',
        'bp_large_2',
        'bp_large_express',
        'bp_large_timed',
        'lp_km',
        'lp_price',
        'lp_extra',
    ];
}
