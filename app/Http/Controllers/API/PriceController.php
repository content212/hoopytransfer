<?php

namespace App\Http\Controllers\API;

use App\Log;
use App\Price;
use App\Exports\PriceExport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\QueryException;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prices = Price::query();
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

    public function import(Request $request)
    {

        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('select_file')->getRealPath();

        $data = (new FastExcel)->import($path);
        if ($data->count() > 0) {
            foreach ($data->toArray() as $key => $value) {
                $insert_data[] = array(
                    'area'              => $value['bolge'],
                    'zip_code'          => $value['posta_kodu'],
                    'bp_km_price'       => $value['bp_km_fiyat'],
                    'bp_small_6'        => $value['bp_small_6_saat'],
                    'bp_small_3'        => $value['bp_small_3_saat'],
                    'bp_small_2'        => $value['bp_small_2_saat'],
                    'bp_small_express'  => $value['bp_small_express'],
                    'bp_small_timed'    => $value['bp_small_zamanli'],
                    'bp_medium_6'       => $value['bp_medium_6_saat'],
                    'bp_medium_3'       => $value['bp_medium_3_saat'],
                    'bp_medium_2'       => $value['bp_medium_2_saat'],
                    'bp_medium_express' => $value['bp_medium_express'],
                    'bp_medium_timed'   => $value['bp_medium_zamanli'],
                    'bp_large_6'        => $value['bp_large_6_saat'],
                    'bp_large_3'        => $value['bp_large_3_saat'],
                    'bp_large_2'        => $value['bp_large_2_saat'],
                    'bp_large_express'  => $value['bp_large_express'],
                    'bp_large_timed'    => $value['bp_large_zamanli'],
                    'lp_km'             => $value['lp_km'],
                    'lp_price'          => $value['lp_price'],
                    'lp_extra'          => $value['lp_extra'],
                );
                //error_log(json_encode($insert_data));
            }
        }
        $prices = $insert_data;
        foreach (Price::all('zip_code') as $price) {
            if (array_search($price->zip_code, array_column($prices, 'zip_code')) !== false) {
            } else {
                Price::where('zip_code', '=', $price->zip_code)->first()->delete();
            }
        }
        foreach ($prices as $price) {
            try {
                Price::updateOrCreate([
                    'zip_code' => $price['zip_code']
                ], $price);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
        Log::addToLog('Price Log.', $prices, 'Import');
        return response(['success' => 'ok'], 200);
    }
    public function export(Request $request)
    {
        ob_end_clean();
        ob_start();
        Log::addToLog('Price Log.', 'price_list_' . date("Y_m_d") . '.xlsx', 'Export');
        return (new PriceExport())->download('price_list_' . date("Y_m_d") . '.xlsx');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Price  $price
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
     * @param  \App\Price  $price
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function destroy($price)
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

    public function truncate()
    {
        try {
            Price::query()->truncate();
            Log::addToLog('Price Log.',  Price::query()->truncate(), 'Truncate');
            return response()->json(['message' => 'Data truncate'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
