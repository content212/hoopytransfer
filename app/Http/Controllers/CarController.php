<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

class CarController extends Controller
{
    protected function role()
    {
        try {
            if (isset($_COOKIE['token'])) {
                return Utils::getRole();
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


        if ($role) {
            if ($role === 'Admin') {
                return view('cars', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
