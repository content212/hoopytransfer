<?php

namespace App\Http\Controllers\API;

use App\Log;
use App\User;
use DateTime;
use App\WorkHours;
use Carbon\Carbon;
use App\DriverWorkList;
use App\Exports\WorkExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Lager;
use App\WorkInput;
use App\WorkNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class DriverWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = WorkHours::select('work_hours.id', 'users.name', 'work_hours.date', DB::raw('work_hours.6_18 / 60 as 6_18'), DB::raw('work_hours.18_22/ 60 as 18_22'), DB::raw('work_hours.22_0/ 60 as 22_0'), DB::raw('work_hours.0_6/ 60 as 0_6'), 'work_notes.note')
            ->leftJoin('work_inputs', 'work_inputs.group_id', '=', 'work_hours.group_id')
            ->leftJoin('users', 'users.id', '=', 'work_inputs.user_id')
            ->leftJoin('work_notes', 'work_notes.group_id', '=', 'work_hours.group_id')
            ->groupBy('work_hours.group_id');
        if ($request->get('month') != '') {
            $list = $list->whereMonth('work_hours.date', $request->get('month'));
        }
        if ($request->get('year') != '') {
            $list = $list->whereYear('work_hours.date', $request->get('year'));
        }
        $user = Auth::user();
        if (str_replace(' ', '', $user->role->role) == 'driver') {
            $list = $list->where('user_id', $user->id);
            return DataTables::of($list)

                ->addColumn('edit', function ($row) {
                    $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                        '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm disabled">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['edit'])
                ->make(true);
        } else {
            if ($request->get('driver') != '') {
                $list = $list->where('user_id', $request->get('driver'));
            }
            return DataTables::of($list)
                ->addColumn('edit', function ($row) {
                    $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                        '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['edit'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set('Europe/Istanbul');
        $input = $request->all();
        $date = date("d-m-Y", strtotime($input['date']));
        $today = new DateTime();
        $today = $today->format('d-m-Y');


        $user = Auth::user();

        $total = (strtotime($input['finish']) - strtotime($input['start'])) - strtotime($input['rest']);
        $input['total'] = date('H:i', $total);

        if ($user->role->role == 'driver') {
            $input['user_id'] = $user->id;
            if ($date == $today) {
                try {
                    $driverwork = DriverWorkList::create($input);
                    Log::addToLog('Work Log.', $request->all(), 'Create');
                    return response($driverwork->toJson(JSON_PRETTY_PRINT), 200);
                } catch (QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 400);
                }
            } else {
                return response(['message' => 'You can not add this date!'], 422);
            }
        } else {
            try {
                $driverwork = DriverWorkList::create($input);
                Log::addToLog('Work Log.', $request->all(), 'Create');
                return response($driverwork->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
    }
    public function driverWorkAction(Request $request)
    {
        if ($request->ajax()) {
            if ($request->action == 'edit') {
                $input = $request->all();
                unset($input['action']);
                unset($input['scope']);
                $date = date("Y-m-d", strtotime($input['date']));
                $today = date("Y-m-d");
                $input['date'] = $date;

                $user = Auth::user();

                $total = (strtotime($input['finish']) - strtotime($input['start'])) - strtotime($input['rest']);
                $input['total'] = date('H:i', $total);

                if ($user->role->role == 'driver') {
                    $input['user_id'] = $user->id;
                    if ($date == $today) {
                        try {
                            $driverwork = DriverWorkList::where('id', $request->id)->update($input);
                            Log::addToLog('Work Log.', $request->all(), 'Edit');
                            return response($driverwork, 200);
                        } catch (QueryException $e) {
                            return response()->json(['message' => $e->getMessage()], 400);
                        }
                    } else {
                        return response(['message' => 'You can not add this date!'], 422);
                    }
                } else {
                    try {
                        $driverwork = DriverWorkList::where('id', $request->id)->update($input);
                        Log::addToLog('Work Log.', $request->all(), 'Edit');
                        return response($driverwork, 200);
                    } catch (QueryException $e) {
                        return response()->json(['message' => $e->getMessage()], 400);
                    }
                }
            }
            if ($request->action == 'delete') {
                DriverWorkList::where('id', $request->id)
                    ->delete();
            }
            return response()->json($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$works = WorkHours::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        $start_input = WorkInput::select('datetime', 'user_id')->where('group_id', $works->group_id)->where('type', 'start')->first();
        $finish_input = WorkInput::select('datetime')->where('group_id', $works->group_id)->where('type', 'finish')->first();

        $rest_start_input = WorkInput::select('datetime')->where('group_id', $works->group_id)->where('type', 'rest_start')->first();
        $rest_finish_input = WorkInput::select('datetime')->where('group_id', $works->group_id)->where('type', 'rest_finish')->first();

        $user_id = $start_input->user_id;
        $excuse = $works->excuse;
        $note = null;
        if ($worknote = WorkNote::select('note')->where('group_id', $works->group_id)->first())
            $note = $worknote->note;
        return response([
            'user_id'           => $user_id,
            'start_datetime'    => (new Carbon($start_input->datetime))->format('m/d/Y H:i'),
            'finish_datetime'   => (new Carbon($finish_input->datetime))->format('m/d/Y H:i'),
            'rest_start'        => (new Carbon($rest_start_input->datetime))->format('m/d/Y H:i'),
            'rest_finish'       => (new Carbon($rest_finish_input->datetime))->format('m/d/Y H:i'),
            'excuse'            => $excuse,
            'note'              => $note
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        unset($input['scope']);
        $date = date("Y-m-d", strtotime($input['date']));
        $today = date("Y-m-d");
        $input['date'] = $date;

        $user = Auth::user();

        $total = (strtotime($input['finish']) - strtotime($input['start'])) - strtotime($input['rest']);
        $input['total'] = date('H:i', $total);

        if ($user->role->role == 'driver') {
            if ($date == $today) {
                try {
                    $driverwork = DriverWorkList::where('id', $user->id)->update($input);
                    Log::addToLog('Work Log.', $request->all(), 'Edit');
                    return response($driverwork, 200);
                } catch (QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 400);
                }
            } else {
                return response(['message' => 'You can not add this date!'], 422);
            }
        } else {
            try {
                $driverwork = DriverWorkList::where('id', $id)->update($input);
                Log::addToLog('Work Log.', $request->all(), 'Edit');
                return response($driverwork, 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$job = DriverWorkList::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $job->delete();
            Log::addToLog('Work Log.', $job, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getDriversedit()
    {
        $drivers = User::select('id', 'name')
            ->join('roles', 'users.id', '=', 'roles.user_id')
            ->where('roles.role', 'driver')
            ->get();
        $response = '{';
        $i = 0;
        $len = count($drivers);
        foreach ($drivers as $driver) {
            if ($i == $len - 1) {
                $response = $response . '"' . $driver->id . '":"' . $driver->name . '"';
            } else {
                $response = $response . '"' . $driver->id . '":"' . $driver->name . '",';
            }
            $i++;
        }
        $response = $response . "}";
        return $response;
    }
    public function getDrivers()
    {
        $drivers = User::select('id', 'name')
            ->join('roles', 'users.id', '=', 'roles.user_id')
            ->where('roles.role', 'driver')
            ->get();
        return $drivers->toJson();
    }
    public function getYears(Request $request)
    {
        $years = WorkHours::selectRaw('YEAR(date) as year')->orderBy('year', 'desc');

        return $years->groupBy('year')->get()->toJson(JSON_PRETTY_PRINT);
    }
    public function getMonth(Request $request)
    {
        $months = WorkHours::selectRaw('MONTH(date) as month')->orderBy('month', 'desc');
        return $months->groupBy('month')->get()->toJson(JSON_PRETTY_PRINT);
    }
    public function getLagers(Request $request)
    {
        $lagers = Lager::select('id', 'name')->get();
        return $lagers;
    }

    public function excelExport(Request $request)
    {

        $user = Auth::user();
        if ($request->get('year') != '') {
            $this->year = $request->get('year');
        }
        if ($request->get('month') != '') {
            $this->month = $request->get('month');
        }
        if ($request->get('user_id') != '') {
            $this->user_id = $request->get('user_id');
            $name = User::select('name')->where('id', $this->user_id)->first()->name;
            $this->fileName = $this->month . '_' . $this->year . '_' . $name;
        } else {
            if ($user->role->role == 'driver') {
                $this->user_id = $user->id;
                $name = User::select('name')->where('id', $this->user_id)->first()->name;
                $this->fileName = $this->month . '_' . $this->year . '_' . $name;
            } else {
                $this->user_id = -1;
                $this->fileName = $this->month . '_' . $this->year . '_all_driver';
            }
        }
        if ($request->get('company_id') != '') {
            $this->company_id = $request->get('company_id');
        } else {
            $this->company_id = 1;
        }

        ob_end_clean();
        ob_start();
        Log::addToLog('Work Log.', $this->fileName . '.xlsx', 'Export');
        return (new WorkExport($this->year, $this->month, $this->user_id, $this->company_id))->download($this->fileName . '.xlsx');
    }
}
