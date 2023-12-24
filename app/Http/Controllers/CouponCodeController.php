<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

class CouponCodeController extends Controller
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

    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('couponcodes', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }

    public function online(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('onlinecouponcodes', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
    public function giftcards(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('giftcards', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }


    public function offline(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('offlinecouponcodes', ['role' => $role, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }

    public function offlineDetail($id, Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);

        $name = $this->name();


        if ($role) {
            if ($role === 'Admin') {
                return view('offlinecouponcodesdetail', ['role' => $role, 'id' => $id, 'name' => $name]);
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }
}
