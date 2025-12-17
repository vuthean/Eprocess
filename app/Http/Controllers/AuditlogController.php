<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tasklist;
use Illuminate\Support\Facades\Crypt;
class AuditlogController extends Controller
{
    public function index()
    {
        /**@var User $user */
        $user = Auth::user();
        $result = $user->getAllAuditLogs();

        return view('auditlog.index', compact('result'));
    }
    public function getAuditLogListingData(Request $request){
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
        $user   = Auth::user();
        $record_query =  Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->join('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_status', '!=', '001')
            ->whereRaw('(tasklist.req_recid in (select distinct(a.req_recid) from auditlog a where a.doer_email  = ?))', [$user->email]);
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('tasklist.created_at', '>=', $request->dStart);
        }
        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('tasklist.created_at', '<=', $request->dEnd);
        }
        if(!empty($request->req_num)){
          $record_query = $record_query->where('tasklist.req_recid', 'like', '%'.$request->req_num.'%')
                                       ->orWhere('tasklist.req_name', 'like', '%'.$request->req_num.'%')
                                       ->orWhere('tasklist.req_branch', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('tasklist.req_position', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('tasklist.req_date', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('recordstatus.record_status_description', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('requester.subject', 'like', '%' . $request->req_num . '%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('tasklist.created_at', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('recordstatus.record_status_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
        ->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('recordstatus.record_status_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
        })
        ->skip($start)
        ->take($rowperpage)
        ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $req_recid = '<a href="'.url($record->description . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9" href="javascript:void(0)" style="font-weight: 400;">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5" style="width: 500px;">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body">
                        '.$record->subject.'</span>
                    </span>
                </span>
            </span>
        </a></p>';
            $data_arr[] = array(
                "no" => $start + ($key+1),
                "req_recid" => $req_recid,
                "subject" => $subject,
                "req_name" => $record->req_name,
                "req_branch" => $record->req_branch,
                "req_position" => $record->req_position,
                "formname" => $record->formname,
                "record_status_description" => $record->record_status_description,
                "req_date" => $record->req_date,
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
}
