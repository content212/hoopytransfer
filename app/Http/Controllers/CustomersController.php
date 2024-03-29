<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Utils;

class CustomersController extends Controller
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
        if ($role) {
            if ($role == 'Admin') {
                return view('customers', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }

    public function deletedCustomers()
    {
        $role = str_replace(' ', '', $this->role());

        $name = $this->name();
        if ($role) {
            if ($role == 'Admin') {
                return view('deleted-customers', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
