<?php

namespace App\Http\Controllers\API;

use App\Models\Price;
use App\Models\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prices = Price::select(
            'prices.id',
            DB::raw('car_types.name as type'),
            'prices.start_km',
            'prices.finish_km',
            'prices.opening_fee',
            'prices.km_fee'
        )
            ->join('car_types', 'car_types.id', '=', 'prices.car_type');

        return DataTables::of($prices)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $price = Price::create($request->all());
            Log::addToLog('Price Log.', $request->all(), 'Create');
            return response($price->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function show(int $price)
    {
        if (!$prices = Price::where('id', '=', $price)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($prices->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $price)
    {
        if (!$prices = Price::where('id', '=', $price)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $prices->update($request->all());
            Log::addToLog('Price Log.', $request->all(), 'Edit');
            return response($prices->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $price)
    {
        if (!$prices = Price::where('id', '=', $price)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $prices->delete();
            Log::addToLog('Price Log.',  $prices, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
