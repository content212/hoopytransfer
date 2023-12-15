<?php

namespace App\Http\Controllers\API;

use App\Helpers\ContractHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\CouponCode;
use App\Models\Log;
use App\Models\UserContract;
use App\Models\UserCouponCode;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class CouponCodeController extends Controller
{
    public function addCouponToUser(Request $request)
    {
        $user = $request->user('api');

        if (!$user) {
            return Response()->json([
                'result' => false,
                'message' => 'Invalid user!'
            ], 200);
        }

        $code = $request->get('code');

        if (!$code) {
            return Response()->json([
                'result' => false,
                'message' => 'Coupon Code is required!'
            ], 200);
        }

        $userCouponCode = UserCouponCode::where('code', $code)->first();

        if (!$userCouponCode) {
            return Response()->json([
                'result' => false,
                'message' => 'Coupon Code not found!'
            ], 200);
        }

        if (isset($userCouponCode->user_id)) {
            return Response()->json([
                'result' => false,
                'message' => 'This coupon code has been used before!'
            ], 200);
        }

        $now = date('Y-m-d H:i:s');

        if ($now < $userCouponCode->start_date) {
            return Response()->json([
                'result' => false,
                'message' => 'Coupon code usage date has not started yet!'
            ], 200);
        }

        if ($now > $userCouponCode->expiration_date) {
            return Response()->json([
                'result' => false,
                'message' => 'The coupon code has expired!'
            ], 200);
        }


        DB::beginTransaction();

        try {
            $credit = $user->credit + $userCouponCode->credit;
            DB::update("update users set credit = {$credit} where id = {$user->id}");
            DB::update("update user_coupon_codes set user_id = {$user->id}, date_of_use = NOW() where id = {$userCouponCode->id}");
            DB::insert("insert into user_credit_activities (user_id, user_coupon_code_id, credit, note, activity_type) values ({$user->id},{$userCouponCode->id},{$userCouponCode->credit},'Load Money with Coupon','charge')");
            DB::commit();
            return Response()->json([
                'result' => true,
                'message' => 'The operation completed successfully!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return Response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function index()
    {
        $couponcodes = DB::table('coupon_codes')->get();

        return DataTables::of($couponcodes)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">Edit</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($request->id != -1) {
            if (!$couponcode = CouponCode::where('id', '=', $request->id)->first())
                return response()->json(['message' => 'Not Found!'], 404);
            try {
                $input = $request->all();
                if (!array_key_exists('active', $input)) {
                    $input['active'] = 0;
                }
                $couponcode->update($input);
                Log::addToLog('CouponCode Log.', $request->all(), 'Edit');
                return response($couponcode->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } else {
            try {
                $input = $request->all();
                if (!array_key_exists('active', $input)) {
                    $input['active'] = 0;
                }
                $couponcode = CouponCode::create($input);
                Log::addToLog('CouponCode Log.', $request->all(), 'Create');
                return response($couponcode->toJson(JSON_PRETTY_PRINT), 200);
            } catch (QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }

    }

    public function show($id)
    {
        if (!$contract = CouponCode::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($contract->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function destroy($id)
    {
        $couponcode = CouponCode::where('id', '=', $id)->first();
        if (!$couponcode)
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $couponcode->delete();
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
