<?php

namespace App\Http\Controllers;

use App\Models\Budgethistory;
use App\Models\Documentupload;
use App\Models\Payment;
use App\Models\Paymentbody;
use App\Models\Paymentbottom;
use App\Models\Procurement;
use App\Models\Procurementbody;
use App\Models\Procurementbottom;
use App\Models\Procurementfooter;
use App\Models\Requester;
use App\Models\Tasklist;
use Auth;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
class DeleterecordController extends Controller
{
    public function deleteRecord(Request $request)
    {
        \DB::beginTransaction();
        try {
            $param = $request->param;

            /** check if current payment is from procurement so we need to update paid = N */
            $paymentBodies = Paymentbody::where('req_recid', $param)->get();
            $procumentBodyIds = collect($paymentBodies)->pluck('pr_col_id');
            if ($procumentBodyIds->isNotEmpty()) {
                Procurementbody::whereIn('id', $procumentBodyIds)->update(['paid' => 'N']);
            }

            $param_redirect = Tasklist::where('req_recid', $param)->first();
            if ($param_redirect->req_type == '1') {
                $route = 'form/procurement/new';
            } else {
                $route = 'form/payment/new';
            }
            Tasklist::where('req_recid', $param)->delete();
            Payment::where('req_recid', $param)->delete();
            Paymentbody::where('req_recid', $param)->delete();
            Paymentbottom::where('req_recid', $param)->delete();
            Documentupload::where('req_recid', $param)->delete();
            Budgethistory::where('req_recid', $param)->delete();

            Requester::where('req_recid', $param)->delete();
            Procurement::where('req_recid', $param)->delete();
            Procurementbody::where('req_recid', $param)->delete();
            Procurementfooter::where('req_recid', $param)->delete();
            Procurementbottom::where('req_recid', $param)->delete();

            \DB::commit();
            Session::flash('success', 'record deleted');
            return redirect()->route($route);
        } catch (\Exception $e) {
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function download($uuid)
    {
        try {
            if (Auth::user()->email == '') {
                return redirect()->route('/');
            }
            $doc = Documentupload::where('uuid', $uuid)->firstOrFail();
            $pathToFile = storage_path($doc->filepath);
            return response()->download($pathToFile);
        } catch (\Exception $e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function requestLog(Request $request)
    {
        $result = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_status', '<>', '001')
            // ->whereDate('tasklist.created_at', '>', Carbon::now()->subDays(30))
            ->get();

        return view('tasklist.requestlog', compact('result'));
    }
    public function getRequestLogData(Request $request){
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
       if($columnName == 'req_date'){
        $columnName = 'created_at';
       }
        $record_query = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
        ->join('formname', 'tasklist.req_type', 'formname.id')
        ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
        ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
        ->where('tasklist.req_status', '<>', '001');
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('tasklist.created_at', '>=', $request->dStart);
        }
        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('tasklist.created_at', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
        //   $record_query = $record_query->where('tasklist.req_recid', 'like', '%'.$request->req_num.'%');
        $record_query = $record_query->where('tasklist.req_recid', 'like', '%'.$request->req_num.'%')
                                       ->orWhere('tasklist.req_branch', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('tasklist.req_name', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('tasklist.req_position', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('tasklist.req_date', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('recordstatus.record_status_description', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('requester.subject', 'like', '%' . $request->req_num . '%')
                                       ->orWhere('formname.formname', 'like', '%' . $request->req_num . '%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('tasklist.created_at', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('recordstatus.record_status_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
        ->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('recordstatus.record_status_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
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
                "req_date" => Carbon::parse($record->created_at)->format('Y-m-d  g:i:s') ,
                "record_status_description" => $record->record_status_description,
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
    public function filterRequestLog(Request $request)
    {
       
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        $result = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_status', '<>', '001');
        if (!empty($request->start_date)) {
            $result->whereDate('tasklist.created_at', '>=', $start_date);
        }

        if (!empty($request->end_date)) {
            $result->whereDate('tasklist.created_at', '<=', $end_date);
        }

        if (empty($request->start_date) && empty($request->end_date)) {
            $result->whereDate('tasklist.created_at', '>', Carbon::now()->subDays(30));
        }

        $result = $result->get();
        return view('tasklist.requestlog', compact('result'));
    }
}
