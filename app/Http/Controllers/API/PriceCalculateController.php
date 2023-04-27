<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Models\Price;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        if ($data['km'] < 1) {
            return response(['error' => 'Bad request!'], 400);
        }
        $prices = Price::whereRaw('? between start_km and finish_km', [$data['km']])->get();
        $response = [];
        if (count($prices) > 0) {
            foreach ($prices as $key => $price) {
                $newPrice = [
                    'price_id' => $price->id,
                    'name' => $price->carType->name,
                    'person' => $price->carType->person_capacity,
                    'baggage' => $price->carType->baggage_capacity,
                    'image' => $price->carType->imageUrl(),
                    'price' => $price->opening_fee + ($price->km_fee * $data['km']),
                    'discount_price' => ($price->opening_fee + ($price->km_fee * $data['km'])) * (1.0 - ($price->carType->discount_rate / 100.0)),
                    'free_cancellation' => $price->carType->free_cancellation
                ];
                array_push($response, $newPrice);
            }
        } else {
            return response([
                'errors' => ['not_calculated' => ['Price not calculated.']]
            ], 200);
        }
        return response($response, 200);
    }
}
