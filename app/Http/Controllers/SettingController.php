<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
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
        $settings = Setting::all();

        if ($role) {
            if ($role === 'Admin') {
                return view('settings', [
                    'role' => $role,
                    'name' => $name,
                    'settings' => $settings
                ]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
