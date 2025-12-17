<?php

namespace App\Http\Controllers;

use App\Models\ViewAccountingVoucher;
use App\Models\ViewJournalTracking;
use App\Models\ViewProcurementReportTracking;
use App\Models\ViewReportBudgetTracking;
use App\Models\ViewReportPaymentTrackingRequest;
use App\Models\ViewBankReceiptVoucher;
use App\Models\ViewCashPaymentVoucher;
use App\Models\ViewCashReceiptVoucher;
use App\Models\ViewBankVoucher;
use App\Models\ViewRoportPaymentAndProcurementTracking;
use App\Models\ViewClearAdvanceAndProcurementReporting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function advanceClearProcurementRequestTracking(){
        return view('reports.advanceClear_and_procurement_tracking');
    }
    public function paymentProcurementRequestTracking(){
        return view('reports.payment_and_procurement_tracking');
    }
    public function paymentRequestTracking(Request $request)
    {
        return view('reports.payment_tracking');
    }
    public function procurementRequestTracking()
    {
        return view('reports.procurement_report_tracking');
    }
    public function accountingVoucherRequestTracking()
    {
        return view('reports.accounting_voucher_tracking');
    }
    public function accountingVoucherRequestTrackingSearch(){
        return view('reports.accounting_voucher_search_tracking');
    }
    public function journalVoucherRequestTrackingSearch(){
        return view('reports.journal_voucher_search_tracking');
    }
    public function bankReceiptVoucherRequestTrackingSearch(){
        return view('reports.bank_receipt_voucher_search_tracking');
    }
    public function cashPaymentVoucherRequestTrackingSearch(){
        return view('reports.cash_payment_voucher_search_tracking');
    }
    public function cashReceiptVoucherRequestTrackingSearch(){
        return view('reports.cash_receipt_voucher_search_tracking');
    }
    public function BankVoucherRequestTrackingSearch(){
        return view('reports.bank_voucher_search_tracking');
    }
    public function budgetRequestTracking()
    {
        return view('reports.budget_tracking');
    }

    public function getPaymentRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $record_query = new ViewReportPaymentTrackingRequest();
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('created_at', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('created_at', '<=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9"
            href="javascript:void(0)" style="font-weight: 400;"
            onmouseover="hoverTooltip(`'.$record->subject.'`)">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body"></span>
                    </span>
                </span>
            </span>
        </a></p>';

            /** find request id */
            $cryp = Crypt::encrypt($record->rp_ref_no . '___no');
            $url  = url("form/payment/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->rp_ref_no.'</a>';

            $data_arr[] = array(
                "number"                => $start + ($key+1),
                "rp_ref_no"             => $rp_ref_no,
                "subject"               => $subject,
                "req_date"              => $record->req_date,
                "approve_date"          => $record->approve_date,
                "line_review_date"      => $record->line_review_date,
                "accounting_review_date" => $record->accounting_review_date,
                "accounting_review_name" => $record->accounting_review_name,
                "requester"             => $record->requester,
                "reviewers"             => $record->reviewers,
                "approvers"             => $record->approvers,
                "req_department"        => $record->req_department,
                "ccy"                   => $record->ccy,
                "amount"                => $record->amount,
                "supplier_name"         => $record->supplier_name,
                "payment_method"        => $record->payment_method,
                "budget_code"           => $record->budget_code,
                "alt_code"              => $record->alt_code,
                "budget_items"          => $record->budget_items,
                "total_budget"          => $record->total_budget,
                "ytd_expense"           => $record->ytd_expense,
                "total_budget_remaining" => $record->total_budget_remaining,
                "paid_date"             => $record->paid_date,
                "paid_by"               => $record->paid_by,
                "status"                => $record->status,
                'due_date'              => $record->due_date,
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
    public function getClearAdvanceAndProcurementRequestTrackingData(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $record_query = new ViewClearAdvanceAndProcurementReporting();
        if(!empty($request->req_dep)){
            $record_query = $record_query->where('req_branch', 'like', '%'.$request->req_dep.'%');
            
        }
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('req_date', '>=', $request->dStart);
        }
        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('req_date', '<=', $request->dEnd);
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_dep)){
            $record_query = $record_query->whereDate('req_date', '=', $request->dStart);
        }
        
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('adc_ref', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('adc_ref', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        });
       
        if ($rowperpage == -1) {
            $records =  $records->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9"
            href="javascript:void(0)" style="font-weight: 400;"
            onmouseover="hoverTooltip(`'.$record->subject.'`)">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body"></span>
                    </span>
                </span>
            </span>
        </a></p>';

            /** find request id */
            $cryp = Crypt::encrypt($record->adc_ref . '___no');
            $url  = url("form/clear-advances/detail/{$cryp}");
            $adc_ref = '<a href="'.$url.'"​>'.$record->adc_ref.'</a>';

            $data_arr[] = array(
                "number"                => $start + ($key+1),
                "adc_ref"               => $adc_ref,
                "subject"               => $subject,
                "req_date"              => $record->req_date,
                "approve_date"          => $record->approved_date,
                "requester"             => $record->requester,
                "approvers"             => $record->approver,
                "req_department"        => $record->req_branch,
                "department_code"       => $record->department_code,
                "description"           => $record->description,
                "qty"                   => $record->quantity,
                "unit_price"            => $record->unit,
                "advance_ref_no"      => $record->advance_ref_no,
                "adv_requester"         => $record->adv_requester,
                "adv_req_date"         => $record->adv_req_date,
                "adv_paid_date"            => $record->adv_paid_date,
                'adv_paid_by'           =>$record->adv_paid_by,
                "ccy"                   => $record->ccy,
                "amount"                => $record->total_amount_usd,
                "supplier_name"         => $record->supplier_name,
                "payment_method"        => $record->payment_method,
                "budget_code"           => $record->budget_code,
                "alt_code"              => $record->alternative_budget_code,
                "paid_date"             => $record->paid_date,
                "paid_by"               => $record->paid_by,
                "status"                => $record->record_status_description,
                'procurement_req'       =>$record->procurement_req,
                'procurement_req_date'  =>$record->procurement_req_date,
                'procurement_requester' =>$record->procurement_requester,
                'procurement_paid_date' =>$record->procurement_paid_date,
                'procurement_paid_by'   =>$record->procurement_paid_by,
                'req_pr_branch'         =>$record->req_pr_branch,
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
    public function getPaymentAndProcurementRequestTrackingData(Request $request){
        
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $record_query = new ViewRoportPaymentAndProcurementTracking();
        if(!empty($request->req_dep)){
            $record_query = $record_query->where('req_department', 'like', '%'.$request->req_dep.'%');
            
        }
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('created_at', '>=', $request->dStart);
        }
        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('created_at', '<=', $request->dEnd);
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_dep)){
            $record_query = $record_query->whereDate('created_at', '=', $request->dStart);
        }
        
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        });
       
        if ($rowperpage == -1) {
            $records =  $records->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->get();
        }
        $data_arr = [];
        foreach ($records as $key => $record) {
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9"
            href="javascript:void(0)" style="font-weight: 400;"
            onmouseover="hoverTooltip(`'.$record->subject.'`)">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body"></span>
                    </span>
                </span>
            </span>
        </a></p>';

            /** find request id */
            $cryp = Crypt::encrypt($record->rp_ref_no . '___no');
            $url  = url("form/payment/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->rp_ref_no.'</a>';

            $data_arr[] = array(
                "number"                => $start + ($key+1),
                "rp_ref_no"             => $rp_ref_no,
                "subject"               => $subject,
                "req_date"              => $record->req_date,
                "approve_date"          => $record->approve_date,
                "requester"             => $record->requester,
                "reviewers"             => $record->reviewers,
                "approvers"             => $record->approvers,
                "req_department"        => $record->req_department,
                "department_code"       => $record->department_code,
                "description"           => $record->description,
                "qty"                   => $record->qty,
                "unit_price"            => $record->unit_price,
                "ref"                   => $record->ref,
                "pro_request_date"      => $record->pro_request_date,
                "received_date"         => $record->received_date,
                "pro_requester"         => $record->pro_requester,
                "procure_by"            => $record->procure_by,
                "ccy"                   => $record->ccy,
                "amount"                => $record->amount,
                "supplier_name"         => $record->supplier_name,
                "payment_method"        => $record->payment_method,
                "budget_code"           => $record->budget_code,
                "alt_code"              => $record->alt_code,
                "paid_date"             => $record->paid_date,
                "paid_by"               => $record->paid_by,
                "status"                => $record->status,
                "req_pr_branch"         => $record->req_pr_branch
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
    public function filterPaymentReportTracking(Request $request)
    {
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        $reports =ViewReportPaymentTrackingRequest::orderByDesc('created_at');
        if (!empty($request->start_date)) {
            $reports->whereDate('created_at', '>=', $start_date);
        }

        if (!empty($request->end_date)) {
            $reports->whereDate('created_at', '<=', $end_date);
        }

        if (empty($request->start_date) && empty($request->end_date)) {
            $reports->whereDate('created_at', '>', Carbon::now()->subDays(30));
        }

        $reports = $reports->get();
        return view('reports.payment_tracking', compact('reports'));
    }

    public function getAccountingVoucherRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = new ViewAccountingVoucher();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/bank-payment-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';
            $user   = Auth::user();
            $status = $record->record_status_description;
            $action = '';
            if ($record->record_status_description == 'Approved') {
                if($user->fullname == $record->requester){
                    $url_udpate = url("form/bank-payment-vouchers/update-status-request/{$cryp}");
                    $action ='<a data-toggle="modal" data-target="#editmember-Modal" class="btn btn-sm update-status"
                    data-recid="'.$record->req_recid.'">
                    <i class="feather icon-edit"></i></a>';
                }else{
                    $action = '';
                }
                $status = 'Paid';
            }
            if($record->record_status_description == 'Approve'){
                $status = 'Approved';
                $action = '';
            }
            

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
                'action' => $action,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
    public function getBankReceiptVoucherRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = new ViewBankReceiptVoucher();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/bank-receipt-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            $status = $record->record_status_description;
            $user   = Auth::user();
            $action = '';
            if ($record->record_status_description == 'Approved') {
                if($user->fullname == $record->requester){
                    $action ='<a data-toggle="modal" data-target="#editmember-Modal" class="btn btn-sm update-status"
                    data-recid="'.$record->req_recid.'">
                    <i class="feather icon-edit"></i></a>';
                }else{
                    $action = '';
                }
                $status = 'Paid';
            }
            if($record->record_status_description == 'Approve'){
                $status = 'Approved';
                $action = '';
            }

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
                'action' => $action,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
    public function getBankVoucherRequestTrackingData (Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = new ViewBankVoucher();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/bank-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            $status = $record->record_status_description;
            if ($record->record_status_description == 'Approve') {
                $status = 'Pending';
                ;
            }

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }


    public function getJournalVoucherRequestTrackingData(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $record_query = new ViewJournalTracking();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/journal-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            $status = $record->record_status_description;
            $user   = Auth::user();
            $action = '';
            if ($record->record_status_description == 'Approved') {
                if($user->fullname == $record->requester){
                    $action ='<a data-toggle="modal" data-target="#editmember-Modal" class="btn btn-sm update-status"
                    data-recid="'.$record->req_recid.'">
                    <i class="feather icon-edit"></i></a>';
                }else{
                    $action = '';
                }
                $status = 'Paid';
            }
            if($record->record_status_description == 'Approve'){
                $status = 'Approved';
                $action = '';
            }

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
                'action' => $action,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }

    public function getProcurementRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $if_null = 'NA';

        $record_query = new ViewProcurementReportTracking();
        if (empty($request->dStart) and empty($request->dEnd) and empty($request->req_dep)) {
            $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_dep)){
            $record_query = $record_query->where('req_branch', 'like', '%'.$request->req_dep.'%');
            
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('id', 'asc')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('id', 'ASC')
                ->get();
        }
        $data_arr = [];
        foreach ($records as $key => $record) {
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9"
            href="javascript:void(0)" style="font-weight: 400;"
            onmouseover="hoverTooltip(`'.$record->subject.'`)">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body"></span>
                    </span>
                </span>
            </span>
            </a></p>';

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/procurement/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';
            if($record->bid=='no'){
                $sole_source = 'NO';
            }else{
                $sole_source = 'YES';
            }
            if($record->vat == 'vat'){
                $vat = number_format((float)$record->total_usd*0.1, 2, '.', '') ;
                $amount_total_usd = number_format((float)$vat+(float)$record->total_usd);
                $amount_total_khr = number_format((float)$amount_total_usd*4000);
            }else{
                $vat =0;
                $amount_total_usd = $record->total_usd;
                $amount_total_khr = $record->total_khr;
            }
            if($record->paid == 'NO'){
                $advance_request = '';
                $date_of_adv = '';
                $clear_request = '';
                $date_of_adc = '';
                $payment_date = '';
                $payment_ref_no = '';
            }else{
                if($record->used_by_request == ''){
                    $payment_date = $record->payment_date;
                    $payment_ref_no = $record->payment_ref_no;
                    $advance_request = '';
                    $date_of_adv = '';
                    $clear_request = '';
                    $date_of_adc = '';
                }else{
                    $payment_date = '';
                    $payment_ref_no = '';
                    $advance_request = $record->used_by_request;
                    $date_of_adv = $record->date_of_adv1;
                    $clear_request = $record->clear_request1;
                    $date_of_adc = $record->date_of_adc1;

                }
                
            }
            $data_arr[] = array(
                'no'                => $start + ($key+1),
                'req_recid'         => $rp_ref_no,
                'req_date'          => $record->req_date,
                'requested_date'    => $record->requested_date,
                'approved_date'     => $record->approved_date,
                'requester'         => $record->requester,
                'reviewer'          => $record->reviewer,
                'second_review'     => $record->second_review,
                'third_review'      => $record->third_review,
                'fourth_review'     => $record->fourth_reviewer,
                'approver'          => $record->approver,
                'co_approver'       => $record->co_approver,
                'subject'           => $subject,
                'budget_code'       => $record->budget_code,
                'br_dep_code'       => $record->br_dep_code,
                'alternativebudget_code' => $record->alternativebudget_code,
                'description'       => $record->description,
                'currency'          => 'USD',
                'quantity'          => $record->quantity,
                'unit'              => $record->unit,
                'unit_price'        => $record->unit_price,
                'total_usd'         => $amount_total_usd,
                'total_khr'         => $amount_total_khr,
                'vat'               => $vat,
                'paid'              => $record->paid,
                'procured_by'       => $record->procured_by,
                'payment_date'      => $payment_date,
                'payment_ref_no'    => $payment_ref_no,
                'sole_source'       => $sole_source,
                'advance_request'   =>$advance_request,
                'date_of_adv'       =>$date_of_adv,
                'clear_request'     =>$clear_request,
                'date_of_adc'       =>$date_of_adc
            );
        }
        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }

    public function getBudgetRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $record_query = new ViewReportBudgetTracking();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('created_at', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('created_at', '<=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('budget_code', 'like', '%' . $searchValue . '%');
            $query->orWhere('budget_owner', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('budget_code', 'like', '%' . $searchValue . '%');
            $query->orWhere('budget_owner', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('created_at', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {
            $data_arr[] = array(
                'no'                => $start + ($key+1),
                'budget_code' => $record->budget_code,
                'budget_item' => $record->budget_item,
                'budget_owner' => $record->fullname,
                'total_budget' => $record->total,
                'ytd_payment' => $record->ytd_payment,
                'remaining_payment' => $record->remaining_payment,
                'ytd_procurement' => $record->ytd_procurement,
                'remaining_procurement' => $record->remaining_procurement,
                'year' => $record->year,
                'created_date' => $record->created_at,
            );
        }
        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
    public function getCashPaymentVoucherRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = new ViewCashPaymentVoucher();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/cash-payment-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            $status = $record->record_status_description;
            if ($record->record_status_description == 'Approve') {
                $status = 'Pending';
                ;
            }

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
    public function getCashReceiptVoucherRequestTrackingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = new ViewCashReceiptVoucher();

        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('requested_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('requested_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('requested_date', '=', $request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
            $query->orWhere('record_status_description', 'like', '%' . $searchValue . '%');
        });
        if ($rowperpage == -1) {
            $records =  $records->orderBy('requested_date', 'DESC')->get();
        } else {
            $records = $records->skip($start)
                ->take($rowperpage)
                ->orderBy('requested_date', 'DESC')
                ->get();
        }

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find request id */
            $cryp = Crypt::encrypt($record->req_recid . '___no');
            $url  = url("form/cash-receipt-vouchers/detail/{$cryp}");
            $rp_ref_no = '<a href="'.$url.'"​>'.$record->req_recid.'</a>';

            $status = $record->record_status_description;
            if ($record->record_status_description == 'Approve') {
                $status = 'Pending';
                ;
            }

            $data_arr[] = array(
                'number' => $start + ($key+1),
                'voucher_no' => $rp_ref_no,
                'ref_no' => $record->ref_no,
                'ref_type' => $record->ref_type,
                'req_date' => $record->req_date,
                'review_date' => $record->reviewed_date,
                'approve_date' => $record->approved_date,
                'requester' => $record->requester,
                'reviewer' => $record->reviewer,
                'approver' => $record->approver,
                'ccy' => $record->ccy,
                'amount' => $record->total_amount,
                'account_name' => $record->account_name,
                'payment_method' => $record->payment_method_code,
                'paid_date' => $record->paid_date,
                'paid_by' => $record->paid_by,
                'exported_date' => $record->exported_at,
                'status' => $status,
            );
        }



        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
}