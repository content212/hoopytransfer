<?php

namespace App\Http\Livewire;

use App\Models\Car;
use App\Models\CarType;
use App\Models\Driver;
use Livewire\Component;

class CarDriver extends Component
{
    public $car_type;
    public $drivers = [];
    public $cars = [];
    public $driver, $calendar, $test = false;

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
                ->where('car_types.id', $this->car_type)
                //->where('cars.insurance_date', '>', now())
                ->join('car_types', 'cars.type', '=', 'car_types.id')
                ->get();
        }
        return view('livewire.car-driver')
            ->withCartypes(CarType::orderBy('name')->get())->section('content');
    }

    public function change($value)
    {
        if ($this->car_type != $value) {
            $this->car_type = $value;
        }
        if ($this->calendar)
            $this->test = true;
    }
}
