<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use Illuminate\Http\Request;
use App\Helpers\Utils;
use App\Models\Station;

class CarController extends Controller
{
    protected function role()
    {
        try {
            if (isset($_COOKIE['token'])) {
                return Utils::getRole($_COOKIE['token']);
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function name()
    {
        try {
            if (isset($_COOKIE['token'])) {
                return Utils::getName();
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();
        $car_types = CarType::pluck('name', 'id');
        $stations = Station::pluck('name', 'id');

        if ($role) {
            if ($role === 'Admin') {

                return view('vehicles', [
                    'role' => $role,
                    'name' => $name,
                    'car_types' => $car_types,
                    'stations' => $stations
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
