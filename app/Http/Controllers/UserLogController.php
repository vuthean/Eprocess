<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use DB;
use App\Models\Usermgt;
use Exception;
use Illuminate\Support\Facades\Log;
use Adldap\Laravel\Facades\Adldap;


class UserLogController extends Controller
{
    public function index()
    {
        $logs = UserLog::selectRaw("
            user_logs.*,
            REPLACE(user_logs.model_type, 'App\\\Models\\\','') as module,
            users.fullname ,
            users.email")
        ->join('users', 'users.id', 'user_logs.user_id')
        ->get();
        return view('usertracking.index', compact('logs'));
    }
}
