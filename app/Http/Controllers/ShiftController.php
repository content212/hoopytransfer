<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Driver;
use Illuminate\Http\Request;

class ShiftController extends Controller
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
                return Utils::getName($_COOKIE['token']);
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function drivers()
    {
        try {
            $drivers = Driver::select(
                'drivers.id',
                'users.name'
            )
                ->join('users', 'users.id', '=', 'drivers.user_id')
                ->get();
            return $drivers;
        } catch (\Exception $exception) {
            return null;
        }
    }
    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();
        $drivers = $this->drivers();

        if ($role) {
            if ($role === 'Admin') {
                return view('shiftcalendar', [
                    'role' => $role,
                    'name' => $name,
                    'drivers' => $drivers
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
