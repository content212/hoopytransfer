<?php

namespace App\Http\Controllers\API;

use App\Exports\ExportCouponCode;
use App\Helpers\ContractHelper;
use App\Helpers\CouponCodeHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\CouponCode;
use App\Models\CouponCodeGroup;
use App\Models\Log;
use App\Models\UserContract;
use App\Models\UserCouponCode;
use App\Models\UserCreditActivity;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;


class CouponCodeController extends Controller
{

    public function exportCouponCodes($id)
    {
        ob_end_clean(); // this
        ob_start(); // and this
        return Excel::download(new ExportCouponCode($id), 'coupon-codes.xls');
    }

    public function getCouponCodes()
    {
        $list = CouponCode::where('active', 1)
            ->orderBy('credit', 'asc')
            ->get();
        return response()->json($list);
    }

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

        return CouponCodeHelper::addCreditWithCoupon($user, $userCouponCode);
    }

    public function index()
    {
        $couponcodes = DB::table('coupon_codes')->get();

        return DataTables::of($couponcodes)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">Edit</a>' .
                    '<a data-id="' . $row->id . '" class="delete m-1 btn btn-danger btn-sm ">Delete</a>' .
                    '<a data-id="' . $row->id . '" data-name="' . $row->name . '" class="generate m-1 btn btn-info btn-sm ">Generate</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    public function groups()
    {
        $groups = DB::table('coupon_code_groups')->get();

        return DataTables::of($groups)
            ->addColumn('edit', function ($row) {
                return '<a href="/couponcodes/offline/' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    public function giftcards()
    {
        $online = DB::table('user_coupon_codes')
            ->select('user_coupon_codes.id', 'user_coupon_codes.code', 'user_coupon_codes.credit', 'user_coupon_codes.price', 'user_coupon_codes.created_at', 'user_coupon_codes.date_of_use', 'user_coupon_codes.user_id', DB::raw("CONCAT(users.name ,' ', users.surname) as username"), 'coupon_codes.name')
            ->leftJoin('users', 'users.id', '=', 'user_coupon_codes.user_id')
            ->join('coupon_codes', 'coupon_codes.id', '=', 'user_coupon_codes.coupon_code_id')
            ->join('user_credit_activities', 'user_credit_activities.user_coupon_code_id', '=', 'user_coupon_codes.id')
            ->where('user_credit_activities.is_gift', '1')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($online)
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->editColumn('date_of_use', function ($row) {
                return $row->date_of_use ? with(new Carbon($row->date_of_use))->format('d/m/Y H:i:s') : '';
            })
            ->make(true);
    }

    public function online()
    {
        $online = DB::table('user_coupon_codes')
            ->select('user_coupon_codes.id', 'user_coupon_codes.code', 'user_coupon_codes.credit', 'user_coupon_codes.price', 'user_coupon_codes.created_at', 'user_coupon_codes.date_of_use', 'user_coupon_codes.user_id', DB::raw("CONCAT(users.name ,' ', users.surname) as username"), 'coupon_codes.name')
            ->join('users', 'users.id', '=', 'user_coupon_codes.user_id')
            ->join('coupon_codes', 'coupon_codes.id', '=', 'user_coupon_codes.coupon_code_id')
            ->whereNotNull('user_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($online)
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->editColumn('date_of_use', function ($row) {
                return $row->date_of_use ? with(new Carbon($row->date_of_use))->format('d/m/Y H:i:s') : '';
            })
            ->make(true);
    }

    public function groupDetail($id)
    {

        $groups = DB::table('user_coupon_codes')
            ->select('user_coupon_codes.id', 'user_coupon_codes.code', 'user_coupon_codes.credit', 'user_coupon_codes.price', 'user_coupon_codes.created_at', 'user_coupon_codes.date_of_use', 'user_coupon_codes.user_id', DB::raw("CONCAT(users.name ,' ', users.surname) as username"), 'coupon_codes.name')
            ->leftJoin('users', 'users.id', '=', 'user_coupon_codes.user_id')
            ->join('coupon_codes', 'coupon_codes.id', '=', 'user_coupon_codes.coupon_code_id')
            ->where('coupon_code_group_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($groups)
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->editColumn('date_of_use', function ($row) {
                return $row->date_of_use ? with(new Carbon($row->date_of_use))->format('d/m/Y H:i:s') : '';
            })
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

    public function generate(Request $request)
    {
        $coupon_code_id = $request->get("coupon_code_id");
        $name = $request->get("name");
        $quantity = intval($request->get("quantity"));
        $prefix = $request->get("prefix");
        $character_length = intval($request->get("character_length"));
        $couponCode = CouponCode::where('id', $coupon_code_id)->first();
        $group = CouponCodeGroup::create([
            'name' => $name,
            'prefix' => $prefix,
            'quantity' => $quantity,
            'character_length' => $character_length,
            'coupon_code_id' => $couponCode->id,
        ]);

        if ($group) {
            for ($i = 0; $i < $quantity; $i++) {
                a:
                $code = CouponCodeHelper::generateRandomNumber($character_length, $prefix);
                $userCouponCode = UserCouponCode::where('code', $code)->first();
                if ($userCouponCode) {
                    goto a;
                }
                UserCouponCode::create([
                    'coupon_code_group_id' => $group->id,
                    'coupon_code_id' => $couponCode->id,
                    'code' => $code,
                    'credit' => $couponCode->credit,
                    'price' => $couponCode->price
                ]);
            }
        }
        return response($group->toJson(JSON_PRETTY_PRINT), 200);
    }


}
