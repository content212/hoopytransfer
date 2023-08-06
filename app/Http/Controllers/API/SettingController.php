<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    private function like($str, $searchTerm)
    {
        $searchTerm = strtolower($searchTerm);
        $str = strtolower($str);
        $pos = strpos($str, $searchTerm);
        if ($pos === false)
            return false;
        else
            return true;
    }
    public function save(Request $request)
    {
        $rules = [];
        $messages = [];
        $settings = Setting::all();
        foreach ($settings as $setting) {
            if ($setting->type == "image") {
                if ($setting->code == "homepage_slider_2" or $setting->code == "homepage_slider_3" or $setting->code == "homepage_slider_4") {
                    continue;
                }
                $rules[$setting->code] = 'nullable|sometimes|mimes:jpeg,jpg,bmp,png,ico';
            } else {
                $rules[$setting->code] = 'required';
                $messages[$setting->code . '.required'] = 'Please ente a ' . $setting->name;
            }
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
                if ($setting->type == 'image') {
                    if ($value != "undefined") {
                        $image = $value->store('/', 'images');
                        $setting->value = $image;
                    }
                } else {
                    $setting->value = $value;
                }
                $setting->save();
            }
        }
        return response()->json([
            'message' => 'Save success'
        ], 200);
    }
    public function index()
    {
        $settings = Setting::select('name', 'code', 'value', 'type')->get()->keyBy('code');
        $sliders = array(
            "name" => "Home Page Sliders",
            "code" => "home_page_sliders",
            "type" => "image",
            "values" => array()
        );
        foreach ($settings as $setting) {
            if ($setting->type == 'time') {
                $settings->forget($setting->code);
            }
            if ($setting->type == 'image') {
                if ($setting->value != null) {
                    $setting->value = Storage::disk('images')->url($setting->value);
                }
            }
            if ($this->like($setting->code, "homepage_slider_")) {
                array_push($sliders['values'], (array(
                    "value" => $setting->value,
                )));
                $settings->forget($setting->code);
            }
        }
        $settings['home_page_sliders'] = $sliders;
        return response($settings, 200);
    }
}
