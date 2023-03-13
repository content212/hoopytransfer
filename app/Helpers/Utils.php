<?php

namespace App\Helpers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class Utils
{
    public static function getRole($token = null): ?string
    {
        try {
            if ($token)
                $user = Utils::FindUserWithToken($token);
            else
                $user = Auth::user();
            $role = $user->role->role;
        } catch (\Exception $e) {
            return null;
        }

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
    public static function getName($token = null): ?string
    {
        try {
            if ($token)
                $user = Utils::FindUserWithToken($token);
            else
                $user = Auth::user();
            return $user->name;
        } catch (\Exception $e) {
            return null;
        }
    }
    public static function logout($token): ?string
    {
        $tokenId = (new Parser(new JoseEncoder()))->parse($token)->claims()->all()['jti'];
        Token::where('id', '=', $tokenId)->update(['revoked' => true]);
        return 'Logout succes';
    }

    public static function FindUserWithToken($token)
    {
        $tokenId = (new Parser(new JoseEncoder()))->parse($token)->claims()->all()['jti'];
        $accesstoken = Token::where('id', '=', $tokenId)->first();
        if (Carbon::parse($accesstoken->expires_at) < Carbon::now()) {
            return null;
        }
        $user = User::where('id', '=', $accesstoken->user_id)->first();
        return $user;
    }
}
