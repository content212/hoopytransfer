<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserOtp;

class AuthOtpController extends Controller
{

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generate(Request $request)
    {
        /* Validate Data */
        $user = User::where('phone', $request->phone)->first();
        if (!$user)
            return $this->CreateBadResponse("User not found!");

        /* Generate An verification number */
        $userOtp = $this->generateOtp($request->phone);
        $userOtp->sendSMS($request->phone);

        $content = [
            'user_id' => $userOtp->user_id,
            'message' => "Verification number has been sent on Your Mobile Number."
        ];
        return response(json_encode($content), 200);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateOtp($phone)
    {
        $user = User::where('phone', $phone)->first();

        /* User Does not Have Any Existing verification number */
        $userOtp = UserOtp::where('user_id', $user->id)->latest()->first();

        $now = now();

        if ($userOtp && $now->isBefore($userOtp->expire_at)) {
            return $userOtp;
        }

        /* Create a New verification number */
        return UserOtp::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => $now->addMinutes(10)
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function loginWithOtp(Request $request)
    {
        /* Validation */
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);

        /* Validation Logic */
        $userOtp   = UserOtp::where('user_id', $request->user_id)->where('otp', $request->otp)->first();

        $now = now();
        if (!$userOtp) {
            return $this->CreateBadResponse("Your verification number is not correct");
        } else if ($userOtp && $now->isAfter($userOtp->expire_at)) {
            return $this->CreateBadResponse("Your verification number has been expired");
        }

        $user = User::whereId($request->user_id)->first();

        if ($user) {

            $userOtp->update([
                'expire_at' => now()
            ]);
            $userRole = $user->role()->first();

            if ($userRole) {
                $this->scope = $userRole->role;
            }

            $token = $user->createToken($user->email . '-' . now(), [$this->scope]);

            return response()->json([
                'token' => $token->accessToken,
                'role' => $userRole->role,
                'message' => 'Login success'
            ]);
        }

        return $this->CreateBadResponse("Your verification number is not correct");
    }
    private function CreateBadResponse($message)
    {
        $content = [
            'error' => $message
        ];
        return response(json_encode($content), 400);
    }
}
