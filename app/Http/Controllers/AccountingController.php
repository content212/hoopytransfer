<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Helpers\Utils;

class AccountingController extends Controller
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
    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();

        if ($role) {
            if ($role === 'Admin') {
                return view('accountingAll', [
                    'role' => $role,
                    'name' => $name
                ]);
            } elseif ($role === 'Driver') {
                $driver = Driver::firstWhere('user_id', Utils::FindUserWithToken($_COOKIE['token'])->id);

                return view('accountingAll', [
                    'role' => $role,
                    'name' => $name,
                    'id' => $driver->id,
                    'driverName' => $driver->user->name
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
    public function driverIndex(Request $request, int $id)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();
        $driver = Driver::find($id);
        if ($role) {
            if ($role === 'Admin' and $driver) {
                return view('accountingAll', [
                    'role' => $role,
                    'name' => $name,
                    'id' => $id,
                    'driverName' => $driver->user->name
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
    public function detailIndex(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();

        if ($role) {
            if ($role === 'Admin') {
                return view('accountingAll', [
                    'role' => $role,
                    'name' => $name,
                    'id' => -1,
                    'driverName' => 'Detail'
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
