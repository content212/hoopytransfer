<?php

namespace App\Http\Controllers\API;

use App\Log;
use App\PriceList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\PriceCompany;
use Illuminate\Database\QueryException;

class PriceListController extends Controller
{
    public function index(Request $request)
    {
        $pricelist = PriceList::select(
            'price_lists.id',
            DB::raw('(CASE is_active
                when 0 then "No"
                when 1 then "Yes" END) as active'),
            'price_companies.name',
            'price_lists.start_date',
            'price_lists.end_date'
        )
            ->join('price_companies', 'price_companies.id', '=', 'price_lists.company_id');
        if ($request->get('archive') == '')
            $pricelist = $pricelist->where('is_active', 1);
        return DataTables::of($pricelist)
            ->addColumn('edit', function ($row) {
                if ($row->active == "Yes") {
                    $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
                    return $btn;
                }
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date ? with(new Carbon($row->end_date))->format('d/m/Y') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }
    public function show(int $id)
    {
        if (!$pricelist = PriceList::select('price_lists.*', 'price_companies.name')->join('price_companies', 'price_companies.id', '=', 'price_lists.company_id')->where('price_lists.id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($pricelist->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function store(Request $request)
    {
        $lastprice = PriceList::where('company_id', $request->company_id)->where('is_active', 1)->first();
        $today = Carbon::now();
        if ($lastprice) {
            $lastprice->update(['end_date' => $today->toDateString(), 'is_active' => 0]);
        }
        $input = $request->all();
        $input['start_date'] = $today->toDateString();
        try {
            $pricelist = PriceList::create($input);
            return response()->json(['ok'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
        }
    }

    public function update(Request $request, int $id)
    {
        if (!$pricelist = PriceList::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $pricelist->update($request->all());
            Log::addToLog('Price List Log.', $request->all(), 'Edit');
            return response($pricelist->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function destroy(int $id)
    {
        if (!$pricelist = PriceList::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $pricelist->delete();
            Log::addToLog('Price List Log.', $pricelist, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function action(Request $request)
    {
        if ($request->id != -1) {
            if (!$pricelist = PriceList::where('id', '=', $request->id)->first())
                return response()->json(['message' => 'Not Found!'], 404);
            $lastprice = PriceList::where('company_id', $request->company_id)->where('is_active', 1)->first();
            $today = Carbon::now();
            if ($lastprice) {
                $lastprice->update(['end_date' => $today->toDateString(), 'is_active' => 0]);
            }
            $input = $request->all();
            $input['start_date'] = $today->toDateString();
            $input['is_active'] = 1;
            unset($input['id']);
            try {
                $pricelist = PriceList::create($input);
                return response()->json(['ok'], 200);
            } catch (QueryException $e) {
                return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
            }
        } else {
            $lastprice = PriceList::where('company_id', $request->company_id)->where('is_active', 1)->first();
            $today = Carbon::now();
            if ($lastprice) {
                $lastprice->update(['end_date' => $today->toDateString(), 'is_active' => 0]);
            }
            $input = $request->all();
            $input['start_date'] = $today->toDateString();
            $input['is_active'] = 1;
            unset($input['id']);
            try {
                $pricelist = PriceList::create($input);
                return response()->json(['ok'], 200);
            } catch (QueryException $e) {
                return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
            }
        }
    }

    public function addCorp(Request $request)
    {
        try {
            PriceCompany::create($request->all());
            return response()->json(['ok'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
        }
    }
    public function getCompanies()
    {
        return PriceCompany::select('id', 'name')->get()->toJson();;
    }
    public function getUsingComapnies()
    {
        $companies = PriceList::select('company_id')->groupBy('company_id')->get();
        $result = [];
        foreach ($companies as $company) {
            $pricecompany = PriceCompany::select('id', 'name')->where('id', $company->company_id)->first();
            $data =
                [
                    'id'    => $pricecompany->id,
                    'name'  => $pricecompany->name
                ];
            array_push($result, $data);
        }
        return json_encode($result);
    }
}
