<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Utils;

class BookingsController extends Controller
{
    protected function role()
    {
        return Utils::getRole();
        #try {
        #    if (isset($_COOKIE['token'])) {
        #        
        #    }
        #} catch (\Exception $exception) {
        #    return null;
        #}
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
    public function bookingsDatatable(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();

        if ($role) {
            if ($role === 'Admin' || $role === 'Editor') {
                return view('bookings', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
