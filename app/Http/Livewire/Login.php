<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\API\AuthOtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;

class Login extends Component
{
    public $phone;
    public $user_id;
    public $otp;

    public function generate()
    {
        $request = new Request([
            'phone' => $this->phone
        ]);
        $data = json_decode(((new AuthOtpController)->generate($request))->getContent());
        if (isset($data->error))
            session()->flash('error', $data->error);
        else {
            $this->user_id = $data->user_id;
            session()->flash('message', $data->message);
        }
    }
    public function otplogin()
    {
        $request = new Request([
            'user_id' => $this->user_id,
            'otp' => $this->otp
        ]);
        $data = json_decode(((new AuthOtpController)->loginWithOtp($request))->getContent());
        if (isset($data->error))
            session()->flash('error', $data->error);
        else {
            session()->flash('message', $data->message);
            info($data->token);
            $cookie = Cookie::make('token', $data->token, 60, null, null, false, false);
            Cookie::queue($cookie);
            Redirect::route('login');
        }
    }
    public function render()
    {
        return view('livewire.login');
    }
}
