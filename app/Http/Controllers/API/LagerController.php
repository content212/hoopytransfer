<?php

namespace App\Http\Controllers\API;

use App\Lager;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

class LagerController extends Controller
{
    public function index()
    {
        $lagers =  Lager::select('id', 'name', DB::raw('(CASE isOvertime
        when 0 then "No"
        when 1 then "Yes" END) as isOvertime'));

        return DataTables::of($lagers)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->rawColumns(['edit'])
            ->make(true);
    }
    public function show(int $id)
    {
        if (!$lager = Lager::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($lager->toJson(JSON_PRETTY_PRINT), 200);
    }
    public function action(Request $request)
    {
        if ($request->id != -1) {
            if (!$lager = Lager::where('id', '=', $request->id)->first())
                return response()->json(['message' => 'Not Found!'], 404);
            $input = $request->all();
            $input['isOvertime'] = isset($request->isOvertime[0]) ? 1 : 0;
            if ($input['isOvertime'] == 0)
                unset($input['overtime']);
            try {
                $lager->update($input);
                return $input;
                return response()->json(['ok'], 200);
            } catch (QueryException $e) {
                return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
            }
        } else {
            $input = $request->all();
            $input['isOvertime'] = isset($request->isOvertime[0]) ? 1 : 0;
            if ($input['isOvertime'] == 0)
                unset($input['overtime']);
            try {
                $lager = Lager::create($input);
                return response()->json(['ok'], 200);
            } catch (QueryException $e) {
                return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
            }
        }
    }
}
