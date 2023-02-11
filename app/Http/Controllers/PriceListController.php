<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

class PriceListController extends Controller
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
    protected function companies()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getcompanies');
                $companies = $response->getBody();
                return $companies;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    public function index()
    {
        $role = str_replace(' ', '', $this->role());
        $name = $this->name();
        $companies = json_decode($this->companies());
        if ($role) {
            if ($role == 'Admin') {
                return view('pricelist', ['role' => $role, 'name' => $name, 'companies' => $companies]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
