<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Price;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpSpreadsheet\RichText\ITextElement;

class PriceCalculateController extends Controller
{
    public function calculate(Request $request)
    {
        $data = $request->validate([
            'km' => 'required'
        ]);
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return response(['message' => "Could not connect to the database."], 422);
        }
        $prices = Price::whereRaw('? between start_km and finish_km', [$data['km']])->get();
        if (count($prices) > 0) {
            $response = [];
            foreach ($prices as $key => $price) {
                $newPrice = [
                    'price_id' => $price->id,
                    'name' => $price->carType->name,
                    'person' => $price->carType->person_capacity,
                    'baggage' => $price->carType->baggage_capacity,
                    'image' => $price->carType->imageUrl(),
                    'price' => $price->opening_fee + ($price->km_fee * $data['km']),
                    'discount_price' => ($price->opening_fee + ($price->km_fee * $data['km'])) * (1.0 - ($price->carType->discount_rate / 100.0))
                ];
                array_push($response, $newPrice);
            }
        }
        return $response;
    }
}
