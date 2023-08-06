<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegisterOtp;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthOtpController extends Controller
{

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generate(Request $request)
    {
        $rules = array(
            'country_code' => 'required|starts_with:+',
            'phone' => 'required'
        );
        $messages = array(
            'country_code.required' => 'country_code field is required.',
            'country_code.starts_with' => 'country_code field it should start with +.',
            'phone.required' => 'phone field is required.',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }


        /* Validate Data */
        $user = User::where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->first();
        if (!$user)
            return $this->CreateBadResponse("User not found!");
        $phone = $request->country_code . $request->phone;
        /* Generate An verification number */
        $userOtp = $this->generateOtp($request->phone);

        $userOtp->sendSMS($phone);
        $content = [
            'user_id' => strval($userOtp->user_id),
            'message' => "Verification number has been sent on Your Mobile Number.",
            'otp' => strval($userOtp->otp)
        ];

        return $content;
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
                'user_id' => $user->id,
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
            'phone' => 'required',
            'country_code' => 'required|starts_with:+',

        );
        $messages = array(
            'name.required' => 'Please enter a name.',
            'surname.required' => 'Please enter a surname.',
            'email.required' => 'Please enter a email.',
            'password.required' => 'Please enter a password.',
            'phone.required' => 'Please enter a phone.',
            'country_code.required' => 'country_code field is required.',
            'country_code.starts_with' => 'country_code field it should start with +.',
            'email.unique' => 'Email in use.',
            'phone.unique' => 'Phone in use.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }


        $user = User::where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->first();

        if ($user) {
            $request = new Request([
                'phone' => $request->phone,
                'country_code' => $request->country_code,
            ]);
            return $this->generate($request);
        }


        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['otp'] = rand(123456, 999999);
        $input['expire_at'] = now()->addMinutes(10);

        $registerOtp = RegisterOtp::create($input);
        $phone = $request->country_code . $request->phone;
        $registerOtp->sendSMS($phone);

        $content = [
            'phone' => $registerOtp->phone,
            'message' => "Verification number has been sent on Your Mobile Number.",
            'otp' => $input['otp']
        ];
        return response(json_encode($content), 200);
    }
    public function registerValidate(Request $request)
    {
        $rules = array(
            'phone' => 'required|exists:register_otps,phone',
            'country_code' => 'required|starts_with:+',
            'otp' => 'required'
        );
        $messages = array(
            'phone.required' => 'Please enter a phone.',
            'country_code.required' => 'country_code field is required.',
            'country_code.starts_with' => 'country_code field it should start with +.',
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
        $registerOtp = RegisterOtp::where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->where('otp', $request->otp)->first();

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
            'phone' => $registerOtp->phone,
            'country_code' => $registerOtp->country_code,
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
                'user_id' => $user->id,
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
