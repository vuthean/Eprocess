<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tasklist;
use App\Models\Groupid;
use Response;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Session;

use Illuminate\Support\Facades\Crypt;
class TasklistController extends Controller
{
    public function index(Request $request)
    {
        try {
        
            $email = Auth::user()->email;
            $result = Tasklist::select(
                'tasklist.*',
                'formname.formname',
                'formname.description',
                'requester.subject',
            )
                ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
                ->join('formname', 'tasklist.req_type', 'formname.id')
                ->whereIn('tasklist.next_checker_group', [$email, Auth::user()->group_id])
                ->orwhere('tasklist.req_email', $email)
                ->where('tasklist.req_status', '006')
                ->orderby('tasklist.created_at', 'desc')
                ->get();

            return view('tasklist.index', compact('result'));
        } catch (\Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function getTaskListingData(Request $request){
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
        $record_query =  Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'requester.subject',
        )
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->where(function($query) use($email)
            {
                $query->whereIn('tasklist.next_checker_group', [$email, Auth::user()->group_id,Auth::user()->accounting_voucher_group]);
                $query->orwhere('tasklist.req_email', $email);
                $query->where('tasklist.req_status', '006');
            });   
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy('tasklist.created_at', $columnSortOrder)
        ->where(function($query) use( $searchValue)
        {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
            $query->orWhere('formname.formname', 'like', '%' . $searchValue . '%');
        })
        ->skip($start)
        ->take($rowperpage)
        ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            if($record->formname == 'AdvanceFormRequest'){
                $req_recid = '<a href="'.url('form/advances/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'ClearAdvanceFormRequest'){
                $req_recid = '<a href="'.url('form/clear-advances/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'BankPaymentVourcherRequest'){
                $req_recid = '<a href="'.url('form/bank-payment-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'BankReceiptVourcherRequest'){
                $req_recid = '<a href="'.url('form/bank-receipt-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'CashPaymentVourcherRequest'){
                $req_recid = '<a href="'.url('form/cash-payment-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'CashReceiptVourcherRequest'){
                $req_recid = '<a href="'.url('form/cash-receipt-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'JournalVourcherRequest'){
                $req_recid = '<a href="'.url('form/journal-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else if($record->formname == 'BankVourcherRequest'){
                $req_recid = '<a href="'.url('form/bank-vouchers/show-for-approval' . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }else{
                $req_recid = '<a href="'.url($record->description . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')).'">'.$record->req_recid.'</a>';
            }
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
            if($record->subject==null){
                $subject = 'N/A';
            }
            $data_arr[] = array(
                "no" => $start + ($key+1),
                "req_recid" => $req_recid,
                "subject" => $subject,
                "req_name" => $record->req_name,
                "req_branch" => $record->req_branch,
                "req_position" => $record->req_position,
                "formname" => $record->formname,
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
