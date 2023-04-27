<?php

namespace App\Http\Livewire;

use App\Models\Booking;
use App\Models\Car;
use App\Models\CarType;
use App\Models\Driver;
use Livewire\Component;

class CarDriver extends Component
{
    public $car_type = '';
    public $drivers = [];
    public $cars = [];
    public $driver, $calendar, $test = false, $request = false;
    public Booking $booking;

    protected $listeners = ['setBookingId'];

    public function setBookingId($id)
    {
        $this->booking = Booking::with('data')->find($id);

        if ($this->booking->car_type == null)
            $this->car_type = 'request';
        else
            $this->car_type = CarType::find($this->booking->car_type)->id;
    }

    public function render()
    {
        $this->drivers = Driver::select(
            'drivers.id',
            'users.name'
        )
            ->join('users', 'users.id', '=', 'drivers.user_id')
            ->get();
        if (!empty($this->car_type)) {
            $this->cars = Car::select('cars.id', 'cars.plate')
                ->join('car_types', 'cars.type', '=', 'car_types.id');
            if ($this->car_type != 'request')
                $this->cars = $this->cars->where('car_types.id', $this->car_type);
            $this->cars = $this->cars->get();
        }
        return view('livewire.car-driver')
            ->withCartypes(CarType::orderBy('name')->get())->section('content');
    }

    public function carTypeChange($value)
    {

        if ($this->car_type != $value) {

            $this->car_type = $value;
            //dd($value);

        }

        if ($this->calendar)
            $this->test = true;
    }
}
