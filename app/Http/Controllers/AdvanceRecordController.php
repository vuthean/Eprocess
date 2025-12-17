<?php

namespace App\Http\Controllers;

use App\Enums\ActivityCodeEnum;
use App\Models\Activitydescription;
use App\Models\Auditlog;
use App\Models\User;
use App\Models\ViewAdvanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdvanceRecordController extends Controller
{
    public function index()
    {
        /** find current user  */
        $currentUser = Auth::user();

        /** find allowan user to list down in drop box */
        $paymentUsers = User::join('groupid', 'groupid.email', '=', 'users.email')
            ->select('users.id', 'users.fullname')
            ->whereIn('groupid.group_id', ['GROUP_CFO','GROUP_FINANCE','GROUP_ACCOUNTING'])
            ->get();

        return view('advance_record', compact('paymentUsers', 'currentUser'));
    }

    public function renderPagination(Request $request)
    {
        $draw  = $request->get('draw');
        $start = $request->get("start");

        $rowperpage  = $request->get("length");
        $search_arr  = $request->get('search');
        $searchValue = $search_arr['value'];

        $record_query = new ViewAdvanceRecord();
        /** when request want to query date */
            if ($request->dPaid == 'YES') {
                $record_query = $record_query->where('paid',$request->dPaid);
            }

            if ($request->dPaid == 'NO') {
                Log::info('NO');
                $record_query = $record_query->where('paid',$request->dPaid);
            }

            if ($request->dPaid == 'CANCEL') {
                Log::info('C');
                $record_query = $record_query->where('paid',$request->dPaid);
            }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num) and $request->dPaid == 'filter'){
            $record_query = $record_query->where('req_from', '=', 2);
        }
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('request_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('request_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num)){
            $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%')
                                         ->orwhere('requester', 'like', '%'.$request->req_num.'%')
                                         ->orwhere('department', 'like', '%'.$request->req_num.'%');
        }
        /** total record */
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();

        $records =$record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->get();
        } else {
            $records = $records->skip($start)->take($rowperpage)->get();
        }
        $data_arr = [];

        foreach ($records as $key => $record) {

            /** check total request amount by currency */
            $requestAmount = $record->request_amount_khr;
            if ($record->currency == 'USD') {
                $requestAmount = $record->request_amount_usd;
            }

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("{$record->form_url}/{$cryp}");
            $requestId = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            /**generat action button */
            $actionButton = '<a href="#" class="edit_item" data-toggle="modal"
                data-target="#updatePaymentModal"
                data-req_recid="'.$record->req_recid.'"
                data-req_from="'.$record->req_from.'"
            ><i class="fa fa-edit" style="font-size: 20px;color: #0ac282;"></i></a> ';

            $data_arr[] = array(
                "number"          => $start + ($key + 1),
                "request_date"    => $record->request_date,
                "approval_date"   => $record->approval_date,
                "req_recid"       => $requestId,
                "requester"       => $record->requester,
                "from_department" => $record->department,
                "request_amount"  => $requestAmount,
                "currency"        => $record->currency,
                "cleared"         => $record->cleared,
                "payment_by"      => $record->paid_by,
                "paid"            => $record->paid,
                "paid_date"       => $record->paid_date,
                "action_button"   => $actionButton,
            );
        }
        $response = array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData"               => $data_arr
        );
        return response()->json($response);
    }

    public function updatePayment(Request $request)
    {
        $activitydescription = Activitydescription::firstWhere('activity_type', $request->paid);
        if (!$activitydescription) {
            Log::info('Cannot find activity code. in function update payment for AdvanceRecordController');
            Session::flash('error', "Cannot find activity code.");
            return redirect()->back();
        }

        $user = Auth::user();

        Auditlog::create([
            'req_recid'     => $request->req_recid,
            'doer_email'    => $user->email,
            'doer_name'     => "{$user->firstname} {$user->lastname}",
            'doer_branch'   => $user->department,
            'doer_position' => $user->position,
            'activity_code' => $activitydescription->activity_code,
            'activity_description' => $request->comment,
            'activity_form'        => $request->req_from,
            'activity_datetime'    => $request->paid_date ? Carbon::parse($request->paid_date)->toDayDateTimeString() : Carbon::now()->toDayDateTimeString()
        ]);

        return redirect()->back();
    }
}
