<?php

namespace App\Rules;

use App\Models\Booking;
use App\Models\Car;
use Illuminate\Contracts\Validation\Rule;

class InsuranceExp implements Rule
{
    private $booking = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $booking_id)
    {
        $this->booking = Booking::find($booking_id);
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
        $car_id = $value;
        $car = Car::find($car_id);
        if ($car and $this->booking->car_id != $car_id) {
            return $car->insurance_date > now();
        } else
            return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Vehicle insurance date is expired.';
    }
}
