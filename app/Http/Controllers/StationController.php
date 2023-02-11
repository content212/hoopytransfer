<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
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
        $countries = DB::table('countries')->pluck('name', 'id');
        if ($role) {
            if ($role == 'Admin') {
                return view('stations', [
                    'role' => $role,
                    'name' => $name,
                    'countries' => $countries
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
