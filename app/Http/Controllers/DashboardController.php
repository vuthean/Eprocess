<?php

namespace App\Http\Controllers;

use App\Models\Tasklist;
use App\Models\User;
use App\Myclass\Sendemail;
use Auth;
use Crypt;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Response;
use Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $email = Auth::user()->email;
            $pending = Tasklist::where('req_email', $email)->whereIn('req_status', ['002','003'])->get();
            $reject = Tasklist::where('req_email', $email)->where('req_status', '004')->get();
            $close = Tasklist::where('req_email', $email)->where('req_status', '005')->get();

            $result = DB::table('auditlog')
                ->select(
                    'auditlog.*',
                    'formname.formname',
                    'formname.description',
                    'activitydescription.activity_type',
                    'requester.subject',
                )
                ->leftJoin('requester', 'auditlog.req_recid', 'requester.req_recid')
                ->join('formname', 'auditlog.activity_form', 'formname.id')
                ->join('activitydescription', 'activitydescription.activity_code', 'auditlog.activity_code')
                ->where('auditlog.doer_email', $email)
                ->orderby('auditlog.updated_at', 'desc')
                ->get();

            return view('dashboard.index', compact('result', 'pending', 'reject', 'close'));
        } catch (\Exception$e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function getDashboardListing(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $email = Auth::user()->email;
        $record_query = DB::table('auditlog')
        ->select(
            'auditlog.*',
            'formname.formname',
            'formname.description',
            'activitydescription.activity_type',
            'requester.subject',
        )
        ->leftJoin('requester', 'auditlog.req_recid', 'requester.req_recid')
        ->join('formname', 'auditlog.activity_form', 'formname.id')
        ->join('activitydescription', 'activitydescription.activity_code', 'auditlog.activity_code')
        ->where('auditlog.doer_email', $email);
        $totalRecords=$record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('auditlog.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('auditlog.doer_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
            $query->orWhere('auditlog.activity_datetime', 'like', '%' . $searchValue . '%');
            $query->orWhere('activitydescription.activity_type', 'like', '%' . $searchValue . '%');
        })->count();
        $columnNameModify = 'auditlog.req_recid';
        if ($columnName == 'req_recid') {
            $columnNameModify = 'auditlog.req_recid';
        } elseif ($columnName == 'subject') {
            $columnNameModify = 'requester.subject';
        } elseif ($columnName == 'doer_name') {
            $columnNameModify = 'auditlog.doer_name';
        } elseif ($columnName == 'formname') {
            $columnNameModify = 'formname.formname';
        } elseif ($columnName == 'activity_datetime') {
            $columnNameModify = 'auditlog.activity_datetime';
        } elseif ($columnName == 'activity_type') {
            $columnNameModify = 'activitydescription.activity_type';
        }
        $records =$record_query->orderBy('auditlog.created_at', $columnSortOrder)
        ->where(function ($query) use ($searchValue) {
            $query->where('auditlog.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('auditlog.doer_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
            $query->orWhere('auditlog.activity_datetime', 'like', '%' . $searchValue . '%');
            $query->orWhere('activitydescription.activity_type', 'like', '%' . $searchValue . '%');
        })
        ->skip($start)
        ->take($rowperpage)
        ->get();
        $data_arr = [];
        foreach ($records as $record) {
            $html_req_recid = '<a href="'.url($record->description . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'" id="btn_branch_info">'.$record->req_recid.'</a>';
            $html_subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9" href="javascript:void(0)" style="font-weight: 400;">   <span class="subject">'.$record->subject.'</span>  <span class="tooltip-content5" style="width: 500px;">  <span class="tooltip-text3">   <span class="tooltip-inner2" style="padding: 2px;"> <span class="tooltip_body">'. $record->subject.'</span>  </span>  </span>  </span></a></p>';


            $data_arr[] = array(
                    "req_recid" => $html_req_recid,
                    "subject" => $html_subject,
                    "doer_name" => $record->doer_name,
                    "formname" => $record->formname,
                    "activity_datetime" =>  $record->created_at,
                    "activity_type" =>  $record->activity_type
                );
        }
        $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr
            );
        return response()->json($response);
    }
    public function countStatus(Request $request)
    {
        try {
            $email = $request->email;
            $pending = Tasklist::where('req_email', $email)->whereIn('req_status', ['002','003'])->get();
            $reject = Tasklist::where('req_email', $email)->where('req_status', '004')->get();
            $close = Tasklist::where('req_email', $email)->where('req_status', '005')->get();
            $resubmit = Tasklist::where('req_email', $email)->where('req_status', 3)->get();
            $success['reposnseCode'] = '000';
            $data['pending'] = count($pending);
            $data['reject'] = count($reject);
            $data['close'] = count($close);
            $data['resubmit'] = count($resubmit);
            $success['data'] = $data;

            return Response::json($success, 200);
        } catch (\Exception$e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function reportListing($status)
    {
        $condition = Crypt::decrypt($status);
        $email = Auth::user()->email;
        switch ($condition) {
            case 'pending':
                $result = Tasklist::join('formname', 'tasklist.req_type', 'formname.id')->join('requester', 'requester.req_recid', 'tasklist.req_recid')->where('tasklist.req_email', $email)->whereIn('tasklist.req_status', ['002','003'])->get();
                break;
            case 'reject':
                $result = Tasklist::join('formname', 'tasklist.req_type', 'formname.id')->join('requester', 'requester.req_recid', 'tasklist.req_recid')->where('tasklist.req_email', $email)->where('tasklist.req_status', '004')->get();
                break;
            case 'approved':
                $result = Tasklist::join('formname', 'tasklist.req_type', 'formname.id')->join('requester', 'requester.req_recid', 'tasklist.req_recid')->where('tasklist.req_email', $email)->where('tasklist.req_status', '005')->get();
                break;
        }
        return view('filter.index', compact('result', 'condition'));
    }
    
}
