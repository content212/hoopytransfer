<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Shift;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }
    /**
     * Display to clander the list of resources
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clanderIndex(Request $request)
    {
        $shifts = Shift::whereDate('shift_date', '>=', $request->start)
            ->whereDate('shift_date', '<=', $request->end)
            ->where('driver_id', '!=', NULL)
            ->get();

        $data = array();
        foreach ($shifts as $shift) {
            array_push($data, array(
                "id" => $shift->id,
                "title" => $shift->driver->user->name . " " . $shift->driver->user->surname,
                "start" => $shift->shift_date,
                "backgroundColor" => "green",
                "allDay" => true,
            ));
        }
        return response()->json($data);
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
            $data = $request->all();
            unset($data['scope']);

            $shift = Shift::where('shift_date', '=', $data['shift_date'])
                ->where('queue', '=', $data['queue'])
                ->first();
            if ($shift) {
                $shift->driver_id = $data['driver_id'];
                $shift->update();
                Log::addToLog('Shift Log.', $request->all(), 'Update');
                return response($shift->toJson(JSON_PRETTY_PRINT), 200);
            }
            $shift = Shift::create($data);
            Log::addToLog('Shift Log.', $request->all(), 'Create');
            return response($shift->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function show(string $date)
    {
        if ($date == null) {
            response()->json(['message' => "Date is required!"], 400);
        }
        $shifts = Shift::whereDate('shift_date', '=', $date)
            ->get();
        return response()->json($shifts);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shift $shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
