<?php

namespace App\Http\Controllers;

use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Models\BankPaymentVoucher;
use App\Models\JournalVoucher;
use App\Models\BankReceiptVoucher;
use App\Models\CashPaymentVoucher;
use App\Models\CashReceiptVoucher;
use App\Models\BankVoucher;
use App\Models\Tasklist;
use App\Myclass\AccountingVoucherExport;
use App\Myclass\CashPaymentVoucherExport;
use App\Myclass\JournalVoucherExport;
use App\Myclass\BankReceiptVoucherExport;
use App\Myclass\CashReceiptVoucherExport;
use App\Myclass\BankVoucherExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DEUploadController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            bank_payment_vouchers.department,
            bank_payment_vouchers.exported_at,
            bank_payment_vouchers.payment_method_code,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('bank_payment_vouchers', 'bank_payment_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::BankPaymentVourcherRequest())
            ->where('tasklist.req_status', RequestStatusEnum::Approved());
            // $final_result=[];
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
            // dd($final_result);
        return view('accounting_voucher.DE_upload.index', compact('final_result'));
    }

    public function exportExcel($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'BankPaymentVourcherRequest') {
            $bankPayment = BankPaymentVoucher::firstWhere('req_recid', $req_cid);
            if (!$bankPayment) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $bankPayment->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new AccountingVoucherExport([$req_cid], FormTypeEnum::BankPaymentVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }

    public function exportMultiple(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameForm($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::BankPaymentVourcherRequest()) {
            BankPaymentVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new AccountingVoucherExport($request->vouchers, FormTypeEnum::BankPaymentVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }

    private function getFormType($req_recids)
    {
        $req_cid = $req_recids[0];
        $requestNumbers = explode('-', $req_cid);
        if($requestNumbers[0] == 'BP'){
            return FormTypeEnum::BankPaymentVourcherRequest();
        }
        elseif($requestNumbers[0] == 'BR'){
            return FormTypeEnum::BankReceiptVourcherRequest();
        }elseif($requestNumbers[0] == 'CP'){
            return FormTypeEnum::CashPaymentVourcherRequest();
        }elseif($requestNumbers[0] == 'CR'){
            return FormTypeEnum::CashReceiptVourcherRequest();
        }elseif($req_cid[0] == 'T'){
            return FormTypeEnum::BankVourcherRequest();
        }else{
            return FormTypeEnum::JournalVourcherRequest();
        }
        return '0';
    }
    
    private function isTheSameForm($req_recids)
    {
        $prefix = [];
        for ($i=0;$i<count($req_recids);$i++) {
            $req_cid = $req_recids[$i];
            $requestNumbers = explode('-', $req_cid);
            array_push($prefix, $requestNumbers[0]);
        }
        /** uniq array  */
        $requests = collect($prefix)->unique();
        if (collect($requests)->count() == 1) {
            return true;
        }
        return false;
    }
    private function isTheSameFormTreasury($req_recids)
    {
        $prefix = [];
        for ($i=0;$i<count($req_recids);$i++) {
            $req_cid = $req_recids[$i];
            array_push($prefix, $req_cid[0]);
        }
        /** uniq array  */
        $requests = collect($prefix)->unique();
        if (collect($requests)->count() == 1) {
            return true;
        }
        return false;
    }
    private function getRequestIdFromCryptedString($cryptedString)
    {
        $param_url = Crypt::decrypt($cryptedString);
        $after_split = explode('___', $param_url);
        return $after_split[0];
    }
    public function indexJournal(Request $request){
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            journal_vouchers.department,
            journal_vouchers.exported_at,
            journal_vouchers.payment_method_code,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('journal_vouchers', 'journal_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::JournalVourcherRequest())
            ->where('tasklist.req_status', RequestStatusEnum::Approved());
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
        return view('accounting_voucher.DE_upload.journal_index', compact('final_result'));
    }
    
    public function exportExcelJournal($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'JournalVourcherRequest') {
            $bankPayment = JournalVoucher::firstWhere('req_recid', $req_cid);
            if (!$bankPayment) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $bankPayment->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new JournalVoucherExport([$req_cid], FormTypeEnum::JournalVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }
    public function exportMultipleJournal(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameForm($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::JournalVourcherRequest()) {
            JournalVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new JournalVoucherExport($request->vouchers, FormTypeEnum::JournalVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }
    public function indexBankReceipt(Request $request){
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            bank_receipt_vouchers.department,
            bank_receipt_vouchers.exported_at,
            bank_receipt_vouchers.payment_method_code,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('bank_receipt_vouchers', 'bank_receipt_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::BankReceiptVourcherRequest())
            ->where('tasklist.req_status', RequestStatusEnum::Approved());
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
        return view('accounting_voucher.DE_upload.bank_receipt_index', compact('final_result'));
    }
    public function exportExcelBankReceipt($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'BankReceiptVourcherRequest') {
            $bankPayment = BankReceiptVoucher::firstWhere('req_recid', $req_cid);
            if (!$bankPayment) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $bankPayment->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new BankReceiptVoucherExport([$req_cid], FormTypeEnum::BankReceiptVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }
    public function exportExcelBank($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'BankVourcherRequest') {
            $bank = BankVoucher::firstWhere('req_recid', $req_cid);
            if (!$bank) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $bank->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new BankVoucherExport([$req_cid], FormTypeEnum::BankVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }
    public function exportMultipleBankReceipt(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameForm($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::BankReceiptVourcherRequest()) {
            BankReceiptVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new BankReceiptVoucherExport($request->vouchers, FormTypeEnum::BankReceiptVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }
    public function exportMultipleBank(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameFormTreasury($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::BankVourcherRequest()) {
            BankVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new BankVoucherExport($request->vouchers, FormTypeEnum::BankVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }
    public function indexCashPayment(Request $request)
    {
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            cash_payment_vouchers.department,
            cash_payment_vouchers.exported_at,
            cash_payment_vouchers.payment_method_code,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('cash_payment_vouchers', 'cash_payment_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::CashPaymentVourcherRequest())
            ->where('tasklist.req_status', RequestStatusEnum::Approved());
            // dd($request->req_num);
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
        return view('accounting_voucher.DE_upload.cash_payment_index', compact('final_result'));
    }
    public function indexBank(Request $request)
    {
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            bank_vouchers.department,
            bank_vouchers.exported_at,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('bank_vouchers', 'bank_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::bankVourcherRequest())
            ->where('tasklist.step_number','>=',2);
            // dd($request->req_num);
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
        return view('treasury_voucher.DE_upload.bank_index', compact('final_result'));
    }
    public function indexCashReceipt(Request $request)
    {
        $user   = Auth::user();
        $result = Tasklist::selectRaw(
            'tasklist.*,
            formname.formname,
            formname.description,
            recordstatus.record_status_description,
            requester.subject,
            requester.req_name,
            cash_receipt_vouchers.department,
            cash_receipt_vouchers.exported_at,
            cash_receipt_vouchers.payment_method_code,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
            (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
            (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->join('cash_receipt_vouchers', 'cash_receipt_vouchers.req_recid', '=', 'tasklist.req_recid')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::CashReceiptVourcherRequest())
            ->where('tasklist.req_status', RequestStatusEnum::Approved());
            // dd($request->req_num);
            if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
                $final_result =$result->where('tasklist.req_recid','like','%'.$request->req_num.'%')->get();
            }
            if (!empty($request->dStart)) {
                $final_result = $result->whereDate('tasklist.created_at', '>=', $request->dStart)->get();
            }
            if (!empty($request->dEnd)) {
                $final_result = $result->whereDate('tasklist.created_at', '<=', $request->dEnd)->get();
            }
            if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
                $final_result = $result->whereDate('tasklist.created_at', '=', $request->dEnd)->get();
            }
        return view('accounting_voucher.DE_upload.cash_receipt_index', compact('final_result'));
    }
    public function exportMultipleindexCashPayment(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameForm($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::CashPaymentVourcherRequest()) {
            CashPaymentVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new CashPaymentVoucherExport($request->vouchers, FormTypeEnum::CashPaymentVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }
    public function exportMultipleindexCashReceipt(Request $request)
    {
        /** check all request number must be the same form */
        $isThesameForm = $this->isTheSameForm($request->vouchers);
        if (!$isThesameForm) {
            Session::flash('error', 'You must select the same form');
            return redirect()->back();
        }

        $formType = $this->getFormType($request->vouchers);
        if ($formType == FormTypeEnum::CashReceiptVourcherRequest()) {
            CashReceiptVoucher::whereIn('req_recid', $request->vouchers)->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new CashReceiptVoucherExport($request->vouchers, FormTypeEnum::CashReceiptVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
    }
    public function exportExcelindexCashPayment($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'CashPaymentVourcherRequest') {
            $cashPayment = CashPaymentVoucher::firstWhere('req_recid', $req_cid);
            if (!$cashPayment) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $cashPayment->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new CashPaymentVoucherExport([$req_cid], FormTypeEnum::CashPaymentVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }
    public function exportExcelindexCashReceipt($formType, $cryptedString)
    {
        $req_cid = $this->getRequestIdFromCryptedString($cryptedString);

        if ($formType == 'CashReceiptVourcherRequest') {
            $cashReceipt = CashReceiptVoucher::firstWhere('req_recid', $req_cid);
            if (!$cashReceipt) {
                Session::flash('error', 'Your form is not found, Please contact your administator.');
                return redirect()->back();
            }

            /** update export date to voucher */
            $cashReceipt->update(['exported_at'=>Carbon::now()]);

            $currentDate = Carbon::now();
            return Excel::download(new CashReceiptVoucherExport([$req_cid], FormTypeEnum::CashReceiptVourcherRequest()), "DE_{$currentDate}.xlsx");
        }
        return redirect()->back();
    }
    public function DEUploadData(Request $request){
       
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $record_query = Tasklist::selectRaw(
                        'tasklist.*,
                        formname.formname,
                        formname.description,
                        recordstatus.record_status_description,
                        requester.subject,
                        requester.req_name,
                        cash_payment_vouchers.department,
                        cash_payment_vouchers.exported_at,
                        cash_payment_vouchers.payment_method_code,
                        (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.review where r.req_recid = tasklist.req_recid limit 1) as reviewer_name,
                        (select u.fullname as reviewers from  reviewapprove r join users u on u.email = r.approve where r.req_recid = tasklist.req_recid limit 1) as approver_name,
                        (select a.activity_datetime from auditlog a where a.req_recid = tasklist.req_recid order by created_at desc limit 1) as approval_date',
                    )
                        ->join('formname', 'tasklist.req_type', 'formname.id')
                        ->join('cash_payment_vouchers', 'cash_payment_vouchers.req_recid', '=', 'tasklist.req_recid')
                        ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
                        ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
                        ->where('tasklist.req_type', FormTypeEnum::CashPaymentVourcherRequest())
                        ->where('tasklist.req_status', RequestStatusEnum::Approved())
                        ->get();
                        
        if (!empty($request->dStart)) {
            $record_query = $record_query->whereDate('req_date', '>=', $request->dStart);
        }

        if (!empty($request->dEnd)) {
            $record_query = $record_query->whereDate('req_date', '<=', $request->dEnd);
        }
        if(!empty($request->req_num) and empty($request->dEnd) and empty($request->dStart)){
          $record_query = $record_query->where('req_recid', 'like', '%'.$request->req_num.'%');
        }
        if(empty($request->dEnd) and empty($request->dStart) and empty($request->req_num)){
          $record_query = $record_query->whereDate('req_date', '=', $request->dEnd);
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
    
}
