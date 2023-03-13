<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegisterOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Hash;
use \Validator;

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

        $rules = array(
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        );
        $messages = array(
            'user_id.required' => 'Please enter a user_id.',
            'otp.required' => 'Please enter a otp.',
            'user_id.exists' => 'User not found.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }


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

    public function registerGenerate(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'phone' => 'required|unique:users,phone'
        );
        $messages = array(
            'name.required' => 'Please enter a name.',
            'surname.required' => 'Please enter a surname.',
            'email.required' => 'Please enter a email.',
            'password.required' => 'Please enter a password.',
            'phone.required' => 'Please enter a phone.',
            'email.unique' => 'Email in use.',
            'phone.unique' => 'Phone in use.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {

            if (array_key_exists('phone', $validator->invalid())) {
                $request = new Request([
                    'phone' => $validator->invalid()['phone']
                ]);
                return $this->generate($request);
            }
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['otp'] = rand(123456, 999999);
        $input['expire_at'] = now()->addMinutes(10);

        $registerOtp = RegisterOtp::create($input);

        $registerOtp->sendSMS($input['phone']);

        $content = [
            'phone' => $registerOtp->phone,
            'message' => "Verification number has been sent on Your Mobile Number."
        ];
        return response(json_encode($content), 200);
    }
    public function registerValidate(Request $request)
    {
        $rules = array(
            'phone' => 'required|exists:register_otps,phone',
            'otp' => 'required'
        );
        $messages = array(
            'phone.required' => 'Please enter a usephoner_id.',
            'otp.required' => 'Please enter a otp.',
            'phone.exists' => 'Phone not found.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }


        /* Validation Logic */
        $registerOtp   = RegisterOtp::where('phone', $request->phone)->where('otp', $request->otp)->first();

        $now = now();
        if (!$registerOtp) {
            return $this->CreateBadResponse("Your verification number is not correct");
        } else if ($registerOtp && $now->isAfter($registerOtp->expire_at)) {
            return $this->CreateBadResponse("Your verification number has been expired");
        }

        $request = new Request([
            'name' => $registerOtp->name,
            'surname' => $registerOtp->surname,
            'email' => $registerOtp->email,
            'password' => $registerOtp->password,
            'phone' => $registerOtp->phone
        ]);
        $user = (new UserController)->storeCustomer($request);
        if ($user) {
            $registerOtp->update([
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
                'message' => 'Register success'
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
