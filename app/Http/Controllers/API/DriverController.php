<?php

namespace App\Http\Controllers\API;

use App\Driver;
use App\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = Driver::select(
            'drivers.id as id',
            'users.name as name',
            'cars.plate as plate',
            'users.phone as phone',
            'countries.name as country',
            'states.name as state',
        )
            ->join('cars', 'cars.id', '=', 'drivers.car_id')
            ->join('users', 'users.id', '=', 'drivers.user_id')
            ->join('countries', 'countries.id', '=', 'drivers.country')
            ->join('states', 'states.id', '=', 'drivers.state');
        return DataTables::of($drivers)
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
            $user = (new UserController)->storeDriver($request->all());
            if ($user['message'])
                return response()->json(['message' => $user['message']], 400);
            $input = $request->all();
            $input['user_id'] = $user->id;
            $input['car_id'] = $input['car'];
            $driver = Driver::create($input);
            Log::addToLog('Driver Log.', $request->all(), 'Create');
            return response($driver->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show(int $driver)
    {
        if (!$drivers = Driver::where('id', '=', $driver)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        $user = User::where('id', '=', $drivers['user_id'])->first();
        return response(json_encode(array_merge($user->toArray(), $drivers->toArray())), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $driver)
    {
        if (!$drivers = Driver::where('id', '=', $driver)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $user = User::where('id', '=', $drivers['user_id']);
            $input = $request->all();
            $user_array = [
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['password'] ? Hash::make($input['password']) : ''
            ];
            $user->update(array_filter($user_array));

            $input['car_id'] = $input['car'];
            $drivers->update($input);
            Log::addToLog('Driver Log.', $request->all(), 'Edit');
            return response($drivers->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $driver)
    {
        if (!$drivers = Driver::where('id', '=', $driver)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $user = User::where('id', '=', $drivers['user_id']);
            $drivers->delete();
            $user->delete();
            Log::addToLog('Driver Log.',  $drivers, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
