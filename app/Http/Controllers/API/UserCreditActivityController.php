<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserCreditActivity;
use Illuminate\Http\Request;

class UserCreditActivityController extends Controller
{
    public function GetUserCreditActivities(Request $request)
    {
        $user = $request->user('api');
        if (!$user)
            return response()->json(['message' => 'Not Found!'], 404);
        $act = UserCreditActivity::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($act);
    }
}
