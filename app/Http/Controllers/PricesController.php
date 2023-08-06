<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\CarType;
use Illuminate\Http\Request;

class PricesController extends Controller
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
    public function PricesDatatable(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();

        $car_types = CarType::pluck('name', 'id');
        if ($role) {
            if ($role == 'Admin') {
                return view('prices', ['role' => $role, 'name' => $name, 'car_types' => $car_types]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
