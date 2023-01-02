<?php

namespace App\Http\Controllers\API;

use DB;
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
            'postal_code' => 'required',
            'km' => 'required',
            'delivery_type' => 'required',
            'list' => 'required'
        ]);
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return response(['message' => "Could not connect to the database."], 422);
        }
        try {
            $price = Price::where('zip_code', $data['postal_code'])->firstOrFail();
        } catch (\Exception $ex) {
            return response(['message' => 'Could not found zip code!'], 404);
        }
        try {
            $userType = null;
            if ($request->user_type)
                $userType = $request->user_type;
            $response = [];
            $list = json_decode($data['list']);
            foreach ($list as $item) {
                $lastdata = $this->calculatePrice($item, $price, (int)$data['km'], $data['delivery_type'], $request->user('api'), $userType);
                array_push($response, $lastdata);
            }
            return response($response, 200);
        } catch (\Exception $ex) {
            return response($ex, 500);
        }
    }

    private function calculatePercent($price, $percent)
    {
        if ($percent != 0)
            return $price - ($price * $percent / 100.00);
        return $price;
    }
    private function calculatePlusPercent($price, $percent)
    {
        if ($percent != 0)
            return $price + ($price * $percent / 100.00);
        return $price;
    }
    public function calculatePrice($item, $price, $km, $delivery_type, $requestUser, $userType)
    {

        $discount = 0;
        $tax_rate = 0;
        if (($user = $requestUser) && $requestUser->role->role == 'customer') {
            if ($customer = Customer::where('user_id', $user->id)->first()) {
                $discount = $customer->discount;
                $userType = $customer->type;
            } else {
                $discount = 0;
            }
        }
        if ($userType == 'corporate') {
            $tax_rate = 25;
        }
        if ($item->type == 'lastpall') {
            $km_price = $price->lp_km * (int)$km;
            $cubicmeters_count = ceil($item->cubic_meters / 1.824);
            $kg_count = ceil($item->kg / 600);

            $selected_count = max($cubicmeters_count, $kg_count);

            if ($selected_count > 1) {
                $lastprice = ($price->lp_extra * ($selected_count - 1)) + $price->lp_price;
                $discountprice = $this->calculatePercent($lastprice + $km_price, $discount);
                return [
                    'id'                => $item->id,
                    'price'             => $lastprice + $km_price,
                    'discount_rate'     => $discount,
                    'discount'          => $discountprice - ($lastprice + $km_price),
                    'tax_rate'          => $tax_rate,
                    'tax'               => $this->calculatePlusPercent($discountprice, $tax_rate) - $discountprice,
                    'final_price'       => $this->calculatePlusPercent($discountprice, $tax_rate),
                    'lastpall_count'    => $selected_count
                ];
            } else {
                $lastprice = $price->lp_price;
                $discountprice = $this->calculatePercent($lastprice + $km_price, $discount);
                return [
                    'id'                => $item->id,
                    'price'             => $lastprice + $km_price,
                    'discount_rate'     => $discount,
                    'discount'          => $discountprice - ($lastprice + $km_price),
                    'tax_rate'          => $tax_rate,
                    'tax'               => $this->calculatePlusPercent($discountprice, $tax_rate) - $discountprice,
                    'final_price'       => $this->calculatePlusPercent($discountprice, $tax_rate),
                    'lastpall_count'    => $selected_count
                ];
            }
        } else if ($item->type == 'box') {
            $km_price = $price->bp_km_price;
            $delivery_type = $delivery_type;


            if ($price->area != 'Hela Sverige') {
                $delivery_price = 0;
                $cubicmeters_count = 0;
                $kg_count = 0;
                if ($item->cubic_meters <= 0.25) {
                    $cubicmeters_count = 1;
                } elseif ($item->cubic_meters > 0.25 && $item->cubic_meters <= 1.0) {
                    $cubicmeters_count = 2;
                } elseif ($item->cubic_meters > 1.0 && $item->cubic_meters <= 2.0) {
                    $cubicmeters_count = 3;
                }

                if ($item->kg <= 50) {
                    $kg_count = 1;
                } elseif ($item->kg > 50 && $item->kg <= 100) {
                    $kg_count = 2;
                } elseif ($item->kg > 100 && $item->kg <= 200) {
                    $kg_count = 3;
                }

                $selected_count = max($cubicmeters_count, $kg_count);

                if ($delivery_type == 'time_courier') {
                    if ($selected_count == 1) {
                        $delivery_price = $price->bp_small_timed;
                    } else if ($selected_count == 2) {
                        $delivery_price = $price->bp_medium_timed;
                    } else if ($selected_count == 3) {
                        $delivery_price = $price->bp_large_timed;
                    }
                }
                if ($delivery_type == 'express_courier') {
                    if ($selected_count == 1) {
                        $delivery_price = $price->bp_small_express;
                    } else if ($selected_count == 2) {
                        $delivery_price = $price->bp_medium_express;
                    } else if ($selected_count == 3) {
                        $delivery_price = $price->bp_large_express;
                    }
                }
                if ($delivery_type == 'two_hour_delivery') {
                    if ($selected_count == 1) {
                        $delivery_price = $price->bp_small_2;
                    } else if ($selected_count == 2) {
                        $delivery_price = $price->bp_medium_2;
                    } else if ($selected_count == 3) {
                        $delivery_price = $price->bp_large_2;
                    }
                }
                if ($delivery_type == 'three_hour_delivery') {
                    if ($selected_count == 1) {
                        $delivery_price = $price->bp_small_3;
                    } else if ($selected_count == 2) {
                        $delivery_price = $price->bp_medium_3;
                    } else if ($selected_count == 3) {
                        $delivery_price = $price->bp_large_3;
                    }
                }
                if ($delivery_type == 'six_hour_delivery') {
                    if ($selected_count == 1) {
                        $delivery_price = $price->bp_small_6;
                    } else if ($selected_count == 2) {
                        $delivery_price = $price->bp_medium_6;
                    } else if ($selected_count == 3) {
                        $delivery_price = $price->bp_large_6;
                    }
                }
                $lastprice = $km * $km_price + $delivery_price;
                $discountprice = $this->calculatePercent($lastprice, $discount);
                return [
                    'id'            => $item->id,
                    'price'         => $lastprice,
                    'discount_rate' => $discount,
                    'discount'      => $discountprice - $lastprice,
                    'tax_rate'      => $tax_rate,
                    'tax'           => $this->calculatePlusPercent($discountprice, $tax_rate) - $discountprice,
                    'final_price'   => $this->calculatePlusPercent($discountprice, $tax_rate)
                ];
            } else {
                $km_price = $price->bp_km_price * $km;
                $discountprice = $this->calculatePercent($km_price, $discount);
                return [
                    'id'            => $item->id,
                    'price'         => $km_price,
                    'discount_rate' => $discount,
                    'discount'      => $discountprice - $km_price,
                    'tax_rate'      => $tax_rate,
                    'tax'           => $this->calculatePlusPercent($discountprice, $tax_rate) - $discountprice,
                    'final_price'   => $this->calculatePlusPercent($discountprice, $tax_rate)
                ];
            }
        } else if ($item->type == 'annat_format') {
            return [
                'id'            => $item->id,
                'price'         => 0,
                'discount_rate' => 0,
                'discount'      => 0,
                'tax_rate'      => 0,
                'tax'           => 0,
                'final_price'   => 0
            ];
        }
    }
}
