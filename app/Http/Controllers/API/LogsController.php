<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $logs = Log::join('users', 'users.id', '=', 'logs.user_id')
            ->select('logs.subject', 'logs.query_type', 'logs.url', 'logs.ip', 'users.name', 'logs.created_at');
        return DataTables($logs)
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y h:i:s') : '';
            })
            ->make(true);
    }
}
