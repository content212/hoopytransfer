<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Utils
{
    public static function getRole(): ?string
    {
        $role = Auth::user()->role->role;
        switch ($role) {
            case 'admin':
                $role = 'Admin';
                break;
            case 'editor':
                $role = 'Editor';
                break;
            case 'driver':
                $role = 'Driver';
                break;
            case 'driver_manager':
                $role = 'Driver Manager';
                break;
        }
        return $role;
    }
    public static function getName(): ?string
    {
        return Auth::user()->name;
    }
}
