<?php

namespace App\Http\Controllers\API;

use App\Car;
use App\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = Car::select(
            'cars.id',
            'cars.plate',
            DB::raw('car_types.name as type'),
            'car_types.person_capacity',
            'car_types.baggage_capacity'
        )
            ->join('car_types', 'car_types.id', '=', 'cars.type');

        return DataTables::of($cars)
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
            $car = Car::create($request->all());
            Log::addToLog('Car Log.', $request->all(), 'Create');
            return response($car->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show(int $car)
    {
        if (!$cars = Car::where('id', '=', $car)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($cars->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $car)
    {
        if (!$cars = Car::where('id', '=', $car)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $cars->update($request->all());
            Log::addToLog('Car Log.', $request->all(), 'Edit');
            return response($cars->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $car)
    {
        if (!$cars = Car::where('id', '=', $car)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $cars->delete();
            Log::addToLog('Car Log.',  $cars, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
