<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

class HomeController extends Controller
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

    public function index()
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        if ($role == 'Admin' || $role == 'Editor') {
            return redirect('/bookings?status=0');
        } else if ($role == 'Driver' || $role == 'DriverManager') {
            return redirect('/calendar');
        } else {
            return view('login');
        }
    }
    public function otplogin()
    {
        return view('OTPlogin');
    }
    public function forbidden()
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();
        return view('forbidden', ['role' => $role, 'name' => $name]);
    }
    public function logout(Request $request)
    {
        try {
            #$headers = [
            #    'Authorization' => 'Bearer ' . $_COOKIE['token'],
            #];
            #$client = new \GuzzleHttp\Client([
            #    'headers' => $headers
            #]);
            #$response = $client->post(config('app.url') . '/api/logout');
            Utils::logout($_COOKIE['token']);
            unset($_COOKIE['token']);
            setcookie('token', null, -1, '/');
            return redirect('/');
        } catch (\Exception $th) {
            return response($th);
        }
    }
}
