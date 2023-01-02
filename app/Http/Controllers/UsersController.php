<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected function role()
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

    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('users', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
