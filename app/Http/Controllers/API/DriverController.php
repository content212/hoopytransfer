<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Log;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /* $drivers = Driver::select(
            'drivers.id as id',
            'users.name as name',
            'users.surname as surname',
            'users.phone as phone'
        )
            ->join('users', 'users.id', '=', 'drivers.user_id');
        return DataTables::of($drivers)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->rawColumns(['edit'])
            ->make(true);*/


        $drivers = User::select(DB::raw('(CASE users.status
        when 0 then \'Passive\'
        when 1 then \'Active\' END) as status'), 'users.id', 'drivers.id as driver_id' ,'users.name', 'users.surname', 'users.email', 'users.phone','users.country_code','roles.role','users.created_at')
            ->join('roles', 'roles.user_id', '=', 'users.id')
            ->join('drivers', 'drivers.user_id', '=', 'users.id')
            ->where('roles.role', 'driver');
        return DataTables::of($drivers)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->driver_id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->driver_id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
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
     * @param  \App\Models\Driver  $driver
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
     * @param  \App\Models\Driver  $driver
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
                'surname' => $input['surname'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'country_code' => $input['country_code'],
            ];
            $user->update(array_filter($user_array));

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
