<?php

namespace App\Http\Controllers\API;

use App\Helpers\ContractHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Log;
use App\Models\UserContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class ContractController extends Controller
{
    public function index()
    {
        $contracts = DB::table('contracts')->get();

        return DataTables::of($contracts)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($request->id != -1) {
            if (!$contract = Contract::where('id', '=', $request->id)->first())
                return response()->json(['message' => 'Not Found!'], 404);
            try {
                $input = $request->all();
                if (!array_key_exists('active', $input)) {
                    $input['active'] = 0;
                }
                if (!array_key_exists('required', $input)) {
                    $input['required'] = 0;
                }
                if (!array_key_exists('selected', $input)) {
                    $input['selected'] = 0;
                }
                $contract->update($input);
                Log::addToLog('Contract Log.', $request->all(), 'Edit');
                return response($contract->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } else {
            try {
                $input = $request->all();
                if (!array_key_exists('active', $input)) {
                    $input['active'] = 0;
                }
                if (!array_key_exists('required', $input)) {
                    $input['required'] = 0;
                }
                if (!array_key_exists('selected', $input)) {
                    $input['selected'] = 0;
                }
                $contract = Contract::create($input);
                Log::addToLog('Contract Log.', $request->all(), 'Create');
                return response($contract->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }

    }

    public function show($id)
    {
        if (!$contract = Contract::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($contract->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function destroy($id)
    {
        $contract = Contract::where('id', '=', $id)->first();
        if (!$contract)
            return response()->json(['message' => 'Not Found!'], 404);
        try
        {
            $contract->delete();
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function list()
    {
        $contracts = Contract::where('active', '=', 1)->select('id','name','prefix','suffix','selected','required','display_order','position')
        ->orderBy('display_order','asc')
        ->get();
        return response($contracts, 200);
    }

    public function detail($id)
    {
        $contract = Contract::where('id', '=', $id)->first();

        if (!$contract)
        {
            return response('',404);
        }

        $booking = null;

        if (request()->has('bookingId'))
        {
            $booking = Booking::where('id', '=', request()->query('bookingId'))->first();
        }

        $paymentType = "";

        if (request()->has('paymentType'))
        {
            $paymentType = request()->query('paymentType');
        }

        $layout = '<html>';
        $layout .= '<head>';
        $layout .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $layout .= '</head>';
        $layout .= '<body style="white-space: pre">';
        $layout .= ContractHelper::BuildContract($contract, $booking, $paymentType );
        $layout .= '</body>';
        $layout .= '</html>';

        return response($layout, 200);
    }

    //test
    public function generateUserContract( Request $request)
    {

        $booking;

        $user = $request->user();

        if ($request->booking_id) {
            $booking = Booking::where('id', $request->booking_id )
            ->where('user_id', $user->id)
            ->first();

            if (!$booking) {
                return response()->json(['message' => 'Booking not found!'], 400);
            }
        }

        foreach ($request->contract_ids as $contract_id) {
            $contract = Contract::where('id', $contract_id)
            ->first();

            if (!$contract) {
                return response()->json(['message' => 'Contract not found!'], 400);
            }
        }

        foreach ($request->contract_ids as $contract_id) {
            $contract = Contract::where('id', $contract_id)
            ->first();

            ContractHelper::SaveContract($contract, $booking, $user, $request->payment_type);
        }


        return response('',200);

    }

    public function getUserContractDetail($user_contract_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response('',403);
        }

        $userContract = UserContract::where('id',$user_contract_id)
        ->where('user_id',$user->id)
        ->select('contract')
        ->first();

        if (!$userContract){
            return response('',404);
        }


        $layout = '<html>';
        $layout .= '<head>';
        $layout .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $layout .= '</head>';
        $layout .= '<body style="white-space: pre">';
        $layout .= $userContract->contract;
        $layout .= '</body>';
        $layout .= '</html>';

        return response($layout, 200);


    }
}
