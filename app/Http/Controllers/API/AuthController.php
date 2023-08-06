<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 422);
        }


        if (Auth::attempt($data)) {
            $user = Auth::user();
            if (!$user->status) {
                return response(['message' => 'Your account is not active!'], 422);
            }
            $userRole = $user->role()->first();

            if ($userRole) {
                $this->scope = $userRole->role;
            }

            $token = $user->createToken($user->email . '-' . now(), [$this->scope]);

            return response()->json([
                'token' => $token->accessToken,
                'role' => $userRole->role
            ]);
        } else {
            return response(['message' => 'Incorrect Details.
                Please try again'], 422);
        }
    }
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json([
            'message' => 'Logout succes'
        ]);
    }
    public function getrole()
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
    public function getUsername()
    {
        return Auth::user()->name;
    }
}
