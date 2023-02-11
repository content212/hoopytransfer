<?php

namespace App\Http\Livewire;

use App\CarType;
use App\Driver;
use Livewire\Component;

class CarDriver extends Component
{
    public $car_type;
    public $drivers = [];
    public $driver;

    public function render()
    {
        if (!empty($this->car_type)) {
            $this->drivers = Driver::select(
                'drivers.id',
                'users.name'
            )
                ->join('users', 'users.id', '=', 'drivers.user_id')
                ->join('cars', 'cars.id', '=', 'drivers.car_id')
                ->join('car_types', 'car_types.id', 'cars.type')
                ->where('car_types.id', '=', $this->car_type)
                ->get();
        }
        return view('livewire.car-driver')
            ->withCartypes(CarType::orderBy('name')->get())->section('content');
    }

    public function changeEvent($value)
    {
        $this->car_type = $value;
    }
}
