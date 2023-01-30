<?php

namespace App\Http\Controllers\API;

use App\Station;
use App\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stations = Station::select(
            'stations.id',
            'stations.official_name',
            'stations.official_phone',
            'countries.name',
            'states.name',
        )
            ->join('countries', 'countries.id', '=', 'stations.country')
            ->join('states', 'states.id', '=', 'stations.state');
        return DataTables::of($stations)
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
            $station = Station::create($request->all());
            Log::addToLog('Station Log.', $request->all(), 'Create');
            return response($station->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Station  $station
     * @return \Illuminate\Http\Response
     */
    public function show(int $station)
    {
        if (!$stations = Station::where('id', '=', $station)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($stations->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Station  $station
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $station)
    {
        if (!$stations = Station::where('id', '=', $station)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $stations->update($request->all());
            Log::addToLog('Station Log.', $request->all(), 'Edit');
            return response($stations->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $station)
    {
        if (!$stations = Station::where('id', '=', $station)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $stations->delete();
            Log::addToLog('Station Log.',  $stations, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
