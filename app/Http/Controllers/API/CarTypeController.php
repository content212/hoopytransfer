<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarType;
use App\Models\Log;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CarTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartypetypes = CarType::query();

        return DataTables::of($cartypetypes)
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
            $cartype = CarType::create($request->all());
            Log::addToLog('CarType Log.', $request->all(), 'Create');
            return response($cartype->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CarType  $cartype
     * @return \Illuminate\Http\Response
     */
    public function show(int $cartype)
    {
        if (!$cartypetypes = CarType::where('id', '=', $cartype)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($cartypetypes->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarType  $cartype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $cartype)
    {
        if (!$cartypetypes = CarType::where('id', '=', $cartype)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $cartypetypes->update($request->all());
            Log::addToLog('CarType Log.', $request->all(), 'Edit');
            return response($cartypetypes->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $cartype)
    {
        if (!$cartypetypes = CarType::where('id', '=', $cartype)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $cartypetypes->prices->each->delete();
            $cartypetypes->delete();
            Log::addToLog('CarType Log.',  $cartypetypes, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
