<?php

namespace App\Http\Controllers\API;

use App\Companies;
use App\Helpers\BookingHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDevice;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function TestNotification()
    {
        $booking = Booking::where('id',546)->first();
        BookingHelper::SendNotification($booking, 0);
    }

    public function index()
    {
        $users = User::join('roles', 'roles.user_id', '=', 'users.id')
            ->where ('roles.role', '<>', 'customer')
            ->select(
                'users.id',
                DB::raw('(CASE users.status
                when 0 then \'Passive\'
                when 1 then \'Active\' END) as status'),
                'users.name',
                'users.surname',
                'users.email',
                'users.phone',
                'users.country_code',
                'roles.role',
                'users.created_at'
            );



        return DataTables::of($users)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }
    public function getDrivers()
    {
        $drivers = User::select('id', 'name', 'email', 'phone','country_code')
            ->join('roles', 'roles.user_id', '=', 'users.id')
            ->where('roles.role', 'driver');
        return DataTables::of($drivers)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })->rawColumns(['edit'])
            ->make(true);
    }
    public function getCustomers(Request $request )
    {

        $deleted = $request->get("deleted");

        $users = User::select(DB::raw('(CASE users.status
        when 0 then \'Passive\'
        when 1 then \'Active\' END) as status'), 'users.id', 'users.name', 'users.surname', 'users.email', 'users.phone','users.country_code','roles.role','users.created_at','users.deleted_at', 'users.delete_reason')
            ->join('roles', 'roles.user_id', '=', 'users.id')
            ->where('roles.role', 'customer');


        if ($deleted == "1") {
            $users = $users->where('deleted_at','<>', null);
        }

        return DataTables::of($users)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->editColumn('deleted_at', function ($row) {
                return $row->deleted_at ? with(new Carbon($row->deleted_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }
    public function getCustomer($id)
    {
        return User::select('users.status', 'users.id', 'users.name', 'users.email', 'users.phone','users.country_code' ,'customers.discount', 'customers.type', 'companies.company_name', 'companies.tax_department', 'companies.tax_number', 'companies.organization_number', 'companies.address')
            ->join('customers', 'customers.user_id', '=', 'users.id')
            ->join('companies', 'companies.customer_id', '=', 'customers.id')
            ->where('users.id', '=', $id)
            ->first();
    }
    public function FrontEndCustomer(Request $request)
    {
        $user = $request->user('api');
        if (!$user)
            return response()->json(['message' => 'Not Found!'], 404);

        $dbUser =  User::select('id', 'name', 'surname', 'phone', 'email','country_code','deleted_at')->firstWhere('id', $user->id);

        if ($dbUser->isDeleted()) {
            return response()->json(['message' => 'Not Found!'], 404);
        }

        $userRole = $user->role()->first();

        return response()->json([
            'id' => $dbUser->id,
            'name' => $dbUser->name,
            'surname' => $dbUser->surname,
            'phone' => $dbUser->phone,
            'country_code' => $dbUser->country_code,
            'email' => $dbUser->email,
            'role' => $userRole->role,
        ]);
    }

    public function DeleteAccount(Request $request) {
        $user = $request->user('api');

        if (!$user){
            return response()->json(['message' => 'Not Found!'], 404);
        }
        $userRole = $user->role()->first();

        if ($userRole->role != "customer") {
            return response()->json(['message' => 'Invalid Request!'], 400);
        }

        $delete_reason = $request->get("delete_reason");
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->delete_reason = $delete_reason;
        $user->status = 0;
        $user->save();

        return response()->json(['message' => 'Successfully deleted']);

    }

    public function FrontEndCustomerUpdate(Request $request)
    {

        $rules = array(
            'country_code' => 'required|starts_with:+',
            'phone' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required',
        );
        $messages = array(
            'country_code.required' => 'country_code field is required.',
            'country_code.starts_with' => 'country_code field it should start with +.',
            'phone.required' => 'phone field is required.',
            'name.required' => 'name field is required.',
            'surname.required' => 'surname field is required.',
            'email.required' => 'phone field is required.',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return $this->CreateBadResponse($errors);
        }

        $user = Auth::user();

        if ($user->phone != $request->phone || $user->country_code != $request->country_code) {
            //phone is changed
            $checkPhoneUser = User::where('phone',$request->phone)
            ->where('country_code',$request->country_code)
            ->first();
            if ($checkPhoneUser) {
                return response()->json(['message' => 'This phone number is used by another user!'], 400);
            }
        }


        $id = $user->id;
        if (!$user = User::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $user->update($request->all());
            Log::addToLog('Customer Log.', $request->all(), 'Edit');
            return response($user->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function customersAction(Request $request)
    {

        if ($request->id == -1) {
            $input = $request->all();
            $rules = array(
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
                'surname' => 'required',
                'country_code' => 'required',
                'phone' => 'required|unique:users,phone'
            );
            $validator = Validator::make($request->all(), $rules);
            try {
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->role()->save(Role::create(['role' => 'customer', 'user_id' => $user->id]));
                Log::addToLog('Customer Log.', $request->all(), 'Create');
                return $user;
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } else {
            $input = $request->all();

            if (!$user = User::where('id', '=', $input['id'])->first())
                return response()->json(['message' => 'Not Found!'], 404);
            $input = $request->all();
            $rules = array(
                'email' => 'required|email|unique:users,email,' . $user->id,
                'name' => 'required',
                'surname' => 'required',
                'country_code' => 'required',
                'phone' => 'required|unique:users,phone,' . $user->id
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages()->get('*');
                return response()->json([
                    'error' => $messages
                ], 400);
            }

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user->update($input);
            Log::addToLog('Customer Log.', $request->all(), 'Update');
            return response($user->toJson(JSON_PRETTY_PRINT), 200);
        }
    }
    public function driversAction(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            unset($input['action']);
            unset($input['scope']);
            if ($request->action == 'edit') {
                if (!$user = User::where('id', '=', $request->id)->first())
                    return response()->json(['message' => 'Not Found!'], 404);
                try {
                    $user->update($input);
                    Log::addToLog('Driver Log.', $request->all(), 'Edit');
                    return response($user->toJson(JSON_PRETTY_PRINT), 200);
                } catch (QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 400);
                }
            }
            if ($request->action == 'delete') {
                if (Role::where('user_id', $request->id)->first()->role == 'driver') {
                    Log::addToLog('Driver Log.', $request->all(), 'Delete');
                    User::where('id', $request->id)
                        ->delete();
                } else {
                    return response()->json(['message' => 'You can not delete this Driver!'], 400);
                }
            }
            return response()->json($request);
        }
    }


    public function storeDriver($input)
    {
        try {
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->role()->save(Role::create(['role' => 'driver', 'user_id' => $user->id]));
            return $user;
        } catch (QueryException $e) {
            return ['message' => $e->errorInfo];
        }
    }
    public function storeCustomer(Request $request)
    {
        try {
            $input = $request->all();
            //$input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->role()->save(Role::create(['role' => 'customer', 'user_id' => $user->id]));
            Log::addToLog('Customer Log.', $request->all(), 'Create');
            return $user;
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {

        try {
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->role()->save(Role::create(['role' => $input['role'], 'user_id' => $user->id]));
            Log::addToLog('User Log.', $request->all(), 'Create');
            return response($user->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function action(Request $request)
    {
        if ($request->id != -1) {
            if (!$user = User::where('id', '=', $request->id)->first())
                return response()->json(['message' => 'Not Found!'], 404);
            try {
                $input = $request->all();
                unset($input['password_confirm']);
                $input['password'] = Hash::make($input['password']);
                $user->update($input);
                Log::addToLog('User Log.', $request->all(), 'Edit');
                return response($user->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users'
            ]);
            if ($validator->fails()) {
                $response = $validator->errors();
                session()->flash('flash_error', $response);
                return response()->json(['message' => $response], 400);
            }
            try {
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->role()->save(Role::create(['role' => $input['role'], 'user_id' => $user->id]));
                Log::addToLog('User Log.', $request->all(), 'Create');
                return response($user->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
    }

    public function show($id)
    {
        if (!$user = User::join('roles', 'roles.user_id', '=', 'users.id')->where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);

        return response($user->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function update(Request $request, $id)
    {
        if (!$user = User::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $user->update($request->all());
            return response($user->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function destroy($id)
    {
        if (!$user = User::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $user->delete();
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getAcc()
    {
        return Auth::user();
    }
    public function updateAcc(Request $request)
    {
        try {
            $id = Auth::user()->id;
            $user = User::find($id);
            $input = $request->all();
            if ($input['password'] != '') {
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }
            $user->update($input);
            Log::addToLog('User Log.', $request->all(), 'Edited account.');

            $user->update($request->all());
            return response($user->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function addDevice(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $input  = $request->all();
            $rules = array(
                'device_token' => 'required'
            );
            $messages = array();
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $messages = $validator->messages()->get('*');
                return response()->json([
                    'error' => $messages
                ], 400);
            }

            $user_device = UserDevice::where('device_token','=',$input['device_token'])->first();

            if ($user_device)
            {
                //update
                DB::table('user_devices')
                    ->where('device_token','=', $input['device_token'])
                    ->update([
                    'user_id' => $user_id,
                    'platform' => $input['platform'],
                    'version' => $input['version'],
                    'build_number' => $input['build_number'],
                    'updated_at' => Carbon::now(),
                ]);
            }
            else
            {
                //insert
                DB::table('user_devices')->insert([
                    'user_id' => $user_id,
                    'device_token' => $input['device_token'],
                    'platform' => $input['platform'],
                    'version' => $input['version'],
                    'build_number' => $input['build_number'],
                    'created_at' => Carbon::now(),
                ]);
            }

            return response()->json(['message' => 'Added!'], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function CreateBadResponse($message)
    {
        $content = [
            'error' => $message
        ];
        return response(json_encode($content), 400);
    }
}
