<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Car;

class DriversController extends Controller
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

    public function index()
    {
        $role = str_replace(' ', '', $this->role());

        $name = $this->name();

        $cars = Car::pluck('plate', 'id');
        if ($role) {
            if ($role == 'Admin' || $role == 'Driver' || $role == 'DriverManager') {
                return view('drivers', [
                    'role' => $role,
                    'name' => $name,
                    'cars' => $cars
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
