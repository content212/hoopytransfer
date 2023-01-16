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
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(env("APP_URL", 'http://localhost') . '/api/getName');
                $role = $response->getBody();
                return $role;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function index()
    {
        function role()
        {
            try {
                if (isset($_COOKIE['token'])) {
                    $headers = [
                        'Authorization' => 'Bearer ' . $_COOKIE['token'],
                    ];
                    $client = new \GuzzleHttp\Client([
                        'headers' => $headers
                    ]);
                    $response = $client->get(env("APP_URL", 'http://localhost') . '/api/getRole');
                    $role = $response->getBody();
                    return $role;
                } else {
                    return null;
                }
            } catch (\Exception $exception) {
                return null;
            }
        }
        $role = role();
        $role = str_replace(' ', '', $role);
        if ($role == 'Admin' || $role == 'Editor') {
            return redirect('/bookings?status=0');
        } else if ($role == 'Driver' || $role == 'DriverManager') {
            return redirect('/calendar');
        } else {
            return view('login');
        }
    }
    public function forbidden()
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();
        return view('forbidden', ['role' => $role, 'name' => $name]);
    }
    public function logout()
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $_COOKIE['token'],
            ];
            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);
            $response = $client->post(env("APP_URL", 'http://localhost') . '/api/logout');
            unset($_COOKIE['token']);
            setcookie('token', null, -1, '/');
            return redirect('/');
        } catch (\Exception $th) {
            return response($th);
        }
    }
}
