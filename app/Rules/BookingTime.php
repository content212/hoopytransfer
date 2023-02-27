<?php

namespace App\Rules;

use App\Setting;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Validation\Rule;

class BookingTime implements Rule
{
    private $booking_date = null;
    private $rule = 0;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($booking_date)
    {
        $this->booking_date = $booking_date;
        $rule = Setting::firstWhere('code', 'booking_time')->value;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $datetime = Carbon::parse(new DateTime($this->booking_date . "T" . $value));
        $now = Carbon::parse(now());

        return $datetime->diffInHours($now) < $this->rule;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Booking time must be lower than ' . $this->rule;
    }
}
