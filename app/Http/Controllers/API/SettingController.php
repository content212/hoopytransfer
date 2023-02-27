<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function save(Request $request)
    {
        $rules = [];
        $messages = [];
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $rules[$setting->code] = 'required';
            $messages[$setting->code . '.required'] = 'Please ente a ' . $setting->name;
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages()->get('*');
            return response()->json([
                'error' => $messages
            ], 400);
        }
        foreach ($request->all() as $code => $value) {
            if ($code != 'scope') {
                $setting = Setting::firstWhere('code', $code);
                $setting->value = $value;
                $setting->save();
            }
        }
        return response()->json([
            'message' => 'Save success'
        ], 200);
    }
}
