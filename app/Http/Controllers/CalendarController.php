<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

use function GuzzleHttp\json_decode;

class CalendarController extends Controller
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
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getName');
                $role = $response->getBody();
                return $role;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    protected function getDriversEdit()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getdriversedit');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function getDrivers()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getdrivers');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function getYears()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getyears');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function getMonth()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getmonths');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function getLagers()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/getlagers');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    protected function getCompanies()
    {
        try {
            if (isset($_COOKIE['token'])) {
                $headers = [
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ];
                $client = new \GuzzleHttp\Client([
                    'headers' => $headers
                ]);
                $response = $client->get(config('app.url') . '/api/pricecompanies');
                $drivers = $response->getBody();
                return $drivers;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
    public function index()
    {
        $role = str_replace(' ', '', $this->role());
        $name = $this->name();
        $driversedit = $this->getDriversEdit();
        $drivers = json_decode($this->getDrivers());
        $years = json_decode($this->getYears());
        $months = json_decode($this->getMonth());
        $lagers = json_decode($this->getLagers());
        $companies = json_decode($this->getCompanies());
        if ($role) {
            if ($role == 'Admin' || $role == 'Driver' || $role == 'DriverManager') {
                return view('calendar', ['role' => $role, 'name' => $name, 'driversedit' => $driversedit, 'drivers' => $drivers, 'months' => $months, 'years' => $years, 'lagers' => $lagers, 'companies' => $companies]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
