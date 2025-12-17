<?php

namespace App\Http\Controllers;

use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Models\AdvanceForm;
use App\Models\Auditlog;
use App\Models\BankPaymentVoucher;
use App\Models\BankPaymentVourcherDetail;
use App\Models\ViewJournalVoucherReference;
use App\Models\Branchcode;
use App\Models\Budgetcode;
use App\Models\ClearAdvanceForm;
use App\Models\GeneralLedgerCode;
use App\Models\PaymentMethod;
use App\Models\ProductCode;
use App\Models\RealBranch;
use App\Models\Requester;
use App\Models\SegmentCode;
use App\Models\Supplier;
use App\Models\Tasklist;
use App\Models\TAXCode;
use App\Models\Documentupload;
use App\Models\JournalVoucher;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\BudgetDetail;
use App\Models\Reviewapprove;
use App\Models\ViewBankPaymentVoucherReference;
use App\Models\JournalVourcherDetail;
use App\Traits\Currency;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportJournals;

class JournalVoucherController extends Controller
{
    public function index(){
        $user   = Auth::user();
        $result = Tasklist::select(
            'tasklist.*'
        )
            ->where('tasklist.req_type', FormTypeEnum::JournalVourcherRequest())
            ->where('tasklist.req_email', $user->email)
            ->where('tasklist.req_status', RequestStatusEnum::Save())
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('accounting_voucher.journal.index', compact('result'));
    }
    public function create(){
        $generalLedgerCodes = GeneralLedgerCode::orderBy('account_number', 'desc')->get();
        $brancheCodes       = RealBranch::orderBy('code', 'desc')->get();
        $budgetCodes        = Budgetcode::orderByRaw("CASE
                                                            WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                                ELSE 1
                                                            END")
                                                    ->orderBy('budget_code', 'desc')
                                                    ->get();
        $taxCodes           = TAXCode::orderBy('code', 'desc')->get();
        $supplierCodes      = Supplier::orderBy('code', 'desc')->get();
        $departmentCodes    = Branchcode::orderBy('branch_code', 'desc')->get();
        $productCodes       = ProductCode::orderBy('code', 'desc')->get();
        $segmentCodes       = SegmentCode::orderBy('code', 'desc')->get();
        $paymentMethods     = PaymentMethod::get();
        $references = ViewJournalVoucherReference::get();

        /** current user information */
        $user = Auth::user();
        return view('accounting_voucher.journal.create', compact(
            'generalLedgerCodes',
            'brancheCodes',
            'budgetCodes',
            'taxCodes',
            'supplierCodes',
            'departmentCodes',
            'productCodes',
            'segmentCodes',
            'paymentMethods',
            'references',
            'user'
        ));
    }
    public function saveDraft(Request $request){
        try {
            $totalDRCR = $this->calculateDRCR([
                'amounts'   => $request->amounts,
                'currencies'=> $request->currencies,
                'dr_crs'    => $request->dr_crs,
                'exchange_rate' => $request->exchange_rate,
            ]);

            if ($totalDRCR->totalDR != $totalDRCR->totalCR) {
                Session::flash('error', "TOTAL CREDIT must be equalt to TOTAL DEBIT");
                return redirect()->back();
            }

            $references = '';
            if ($request->has('references')) {
                $references = collect($request->references)->implode(',');
            }

            /** if it is not cross currency then exhcange rate need to set to 1 */
            $exchangeRate = 1;
            if ($this->isCrossCurrency($request->currencies)) {
                $exchangeRate = $request->exchange_rate;
            }
            /**@var JournalVoucher $journal */
            $journal = JournalVoucher::create([
                'ref_no'            =>$references,
                'voucher_number'    =>$request->voucher_number,
                'department'        =>$request->department,
                'request_date'      =>$request->request_date,
                'currency'          =>$request->currency,
                'exchange_rate'     =>$exchangeRate,
                'bank_name'         =>$request->bank_name,
                'account_name'      =>$request->account_name,
                'account_number'    =>$request->account_number,
                'account_currency'  =>$request->account_currency,
                'swift_code'        =>$request->swift_code,
                'beneficiary_number'=>$request->benificiary_name,
                'invoice_number'    =>$request->invoice_number,
                'note'              =>$request->note,
                'payment_method_code' =>$request->payment_method,
            ]);
            $journal->refresh();

            $success = DB::transaction(function () use ($journal, $request) {

            /** create details */
                $journal->createDetails([
                'gl_codes'          =>  $request->gl_codes,
                'branch_codes'      =>  $request->branch_codes,
                'amounts'           =>  $request->amounts,
                'currencies'        =>  $request->currencies,
                'dr_crs'            =>  $request->dr_crs,
                'exchange_rate'     =>  $request->exchange_rate,
                'budget_codes'      =>  $request->budget_codes,
                'al_budget_codes'   =>  $request->al_budget_codes,
                'tax_codes'         =>  $request->tax_codes,
                'supp_codes'        =>  $request->supp_codes,
                'dept_codes'        =>  $request->dept_codes,
                'pro_codes'         =>  $request->pro_codes,
                'seg_codes'         =>  $request->seg_codes,
                'naratives'         =>  $request->naratives,
           ]);

                /** create tasklist */
                $currentUser = Auth::user();
                $tasklist = Tasklist::create([
                'req_recid'          => $journal->req_recid,
                'req_email'          => $currentUser->email,
                'req_name'           => "{$currentUser->firstname} {$currentUser->lastname}",
                'req_branch'         => $currentUser->department,
                'req_position'       => $currentUser->position,
                'req_from'           => FormTypeEnum::JournalVourcherRequest(),
                'req_type'           => FormTypeEnum::JournalVourcherRequest(),
                'next_checker_group' => 1,
                'next_checker_role'  => 1,
                'step_number'        => 1,
                'step_status'        => 1,
                'req_status'         => RequestStatusEnum::Save(),
                'req_date'           => Carbon::now()->toDayDateTimeString(),
                'within_budget'      => 'NOT APPLY',
            ]);

                /** save to requester */
                Requester::create([
                'req_recid'    => $journal->req_recid,
                'req_email'    => $currentUser->email,
                'req_name'     => "{$currentUser->firstname} {$currentUser->lastname}",
                'req_branch'   => $currentUser->department,
                'req_position' => $currentUser->position,
                'req_from'     => FormTypeEnum::JournalVourcherRequest(),
                'req_date'     => Carbon::now()->toDayDateTimeString(),
                'subject'      => $request->invoice_number,
                'ccy'          => $request->currency
            ]);

                /** upload file if hase */
                if ($request->hasFile('fileupload')) {
                    $req_recid = $journal->req_recid;
                    if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                        File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                    }
                    $destinationPath = storage_path() . '/uploads/' . $req_recid . '/';
                    $destinationPath_db = '/uploads/' . $req_recid . '/';

                    $date_time      = Carbon::now()->toDayDateTimeString();
                    foreach ($request->fileupload as $photo) {
                        $file_name = $photo->getClientOriginalName();
                        $photo->move($destinationPath, $file_name);

                        Documentupload::create([
                        'req_recid'         => $req_recid,
                        'filename'          => $file_name,
                        'filepath'          => $destinationPath_db . $file_name,
                        'doer_email'        => $currentUser->email,
                        'doer_name'         => "{$currentUser->firstname} {$currentUser->lastname}",
                        'activity_form'     => FormTypeEnum::JournalVourcherRequest(),
                        'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                        'activity_datetime' => $date_time,
                    ]);
                    }
                }

                return $tasklist;
            });

            if (!$success) {
                Session::flash('error', 'Please contact aministration for check your issue.');
                return redirect()->back();
            }

            /**update total amount of advance form for helping when query report */
            $journal->updateTotalAmount();

            /** block all references form request */
            $journal->blockAllReference();

            return Redirect::to('form/journal-vouchers/edit/' . Crypt::encrypt($journal->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please contact aministration for check your issue.');
            return redirect()->back();
        }

    }

    public function edit($cryptedString)
    {
        $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

        /**@var JournalVoucher $journalPayment */
        $journalPayment = JournalVoucher::firstWhere('req_recid', $req_recid);
        if (!$journalPayment) {
            Session::flash('error', 'Form not found!');
            return redirect()->back();
        }

        /** find references */
        $choosedReferences = [];
        if ($journalPayment->ref_no) {
            $requestIds = explode(',', $journalPayment->ref_no);
            if (count($requestIds) > 0) {
                $choosedReferences = $requestIds;
            }
        }

        $originalReferences =  ViewJournalVoucherReference::get();
        $arrReferences = collect($originalReferences)->map(function ($request) {
            return $request->req_recid;
        })->toArray();
        $references = array_diff($arrReferences, $choosedReferences);

        $journalDetails = JournalVourcherDetail::where('req_recid', $req_recid)->get();

        /** find document uploads */
        $documents = Documentupload::where('req_recid', $req_recid)->get();
        $totalDocument = collect($documents)->count();

        $generalLedgerCodes = GeneralLedgerCode::orderBy('account_number', 'desc')->get();
        $brancheCodes       = RealBranch::orderBy('code', 'desc')->get();
        $budgetCodes = Budgetcode::orderByRaw("CASE
                                          WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                          ELSE 1
                                      END")
                          ->orderBy('budget_code', 'desc')
                          ->get();
        $taxCodes           = TAXCode::orderBy('code', 'desc')->get();
        $supplierCodes      = Supplier::orderBy('code', 'desc')->get();
        $departmentCodes    = Branchcode::orderBy('branch_code', 'desc')->get();
        $productCodes       = ProductCode::orderBy('code', 'desc')->get();
        $segmentCodes       = SegmentCode::orderBy('code', 'desc')->get();
        $paymentMethods     = PaymentMethod::get();
        $user = Auth::user();

        /**if total debit not equal total credit then not allow to view approver */
        $approvalUsers = [];
        $totalDRCR = $journalPayment->getTotalDRCR();
        if ($totalDRCR->total_DR == $totalDRCR->total_CR) {
            $approvalUsers = $journalPayment->getAllApprovers();
        }

        /**find is cross currency */
        $currencies = collect($journalDetails)->pluck('currency');
        $isCrossCurrency = $journalPayment->isCrossCurrency($currencies);
        $defaultCurrency = 'USD';
        if (!$isCrossCurrency) {
            $defaultCurrency = $currencies[0];
        }
        $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
        $al_budgetcode_na = $journalPayment->getBudgetNA($al_budget_codes);
        $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
        /**find is total and YTD */
        $budget_codes = collect($journalDetails)->pluck('budget_code');
        $budgetcode_na = $journalPayment->getBudgetNA($budget_codes);
        $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();


        return view('accounting_voucher.journal.edit', compact(
            'journalPayment',
            'journalDetails',
            'documents',
            'totalDocument',
            'generalLedgerCodes',
            'brancheCodes',
            'budgetCodes',
            'taxCodes',
            'supplierCodes',
            'departmentCodes',
            'productCodes',
            'segmentCodes',
            'paymentMethods',
            'user',
            'references',
            'choosedReferences',
            'approvalUsers',
            'totalDRCR',
            'defaultCurrency',
            'totalAndYTD',
            'budgetcode_na',
            'al_budgetcode_na',
            'totalAndYTDAL'
        ));
    }

    private function getRequestIdFromCryptedString($cryptedString)
    {
        $param_url = Crypt::decrypt($cryptedString);
        $after_split = explode('___', $param_url);
        return $after_split[0];
    }

    public function downloadExcel(){
        $path = public_path('/static/template/journal-voucher-template.xlsx');
        return response()->download($path);
    }
    private function calculateDRCR($items)
    {
        $item = (object)$items;
        $amounts = $item->amounts;
        $currencies = $item->currencies;
        $dr_crs = $item->dr_crs;
        $exchangeRate = (float)$item->exchange_rate;

        /** we need to know if current request is cross currency or not */
        $totalDR = 0;
        $totalCR = 0;
        $isCrossCurrency = $this->isCrossCurrency($currencies);

        if ($isCrossCurrency) {
            for ($i=0;$i<count($amounts);$i++) {
                $currency = $currencies[$i];
                $dr_cr    = $dr_crs[$i];
                $amount   = (float)$amounts[$i];

                if ($dr_cr == 'DEBIT') {
                    $debitAmount = $amount;
                    if ($currency == 'KHR') {
                        $debitAmount = $amount / $exchangeRate;
                    }
                    $totalDR += $debitAmount;
                }

                if ($dr_cr == 'CREDIT') {
                    $creditAmount = $amount;
                    if ($currency == 'KHR') {
                        $creditAmount = $amount / $exchangeRate;
                    }
                    $totalCR += $creditAmount;
                }
            }
        } else {
            for ($i=0;$i<count($amounts);$i++) {
                $currency = $currencies[$i];
                $dr_cr    = $dr_crs[$i];
                $amount   = (float)$amounts[$i];

                if ($dr_cr == 'DEBIT') {
                    $debitAmount = $amount;
                    $totalDR += $debitAmount;
                }

                if ($dr_cr == 'CREDIT') {
                    $creditAmount = $amount;
                    $totalCR += $creditAmount;
                }
            }
        }



        return (object)([
            'totalDR'=> number_format((float)$totalDR, 2, '.', ''),
            'totalCR'=> number_format((float)$totalCR, 2, '.', '')
        ]);
    }
    private function isCrossCurrency($currencies)
    {
        $currency = collect($currencies)->unique();
        if (collect($currency)->count() == 1) {
            return false;
        }
        return true;
    }

    public function submitRequest(Request $request){
        {

            /**@var BankPaymentVoucher $JournalVoucher */
            $JournalVoucher = JournalVoucher::firstWhere('req_recid', $request->req_recid);
            if (!$JournalVoucher) {
                Session::flash('error', "Form not found. Please contact administrator.");
                return redirect()->back();
            }

            /** check if form already submit */
            if ($JournalVoucher->isAlreadySubmitted()) {
                return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($JournalVoucher->req_recid . '___no'));
            }

            /**@var User $user */
            $user = Auth::user();
            if (!$user->isBelongJournalVoucherForm($JournalVoucher)) {
                Session::flash('error', "Make sure you are requester to submit.");
                return redirect()->back();
            }

            $references = '';
            if ($request->has('references')) {
                $references = collect($request->references)->implode(',');
            }

            try {
                $success = DB::transaction(function () use ($JournalVoucher, $request, $references) {
                    $JournalVoucher->update([
                        'ref_no'            =>$references,
                        'voucher_number'    =>$request->voucher_number,
                        'department'        =>$request->department,
                        'request_date'      =>$request->request_date,
                        'currency'          =>$request->currency,
                        'bank_name'         =>$request->bank_name,
                        'account_name'      =>$request->account_name,
                        'account_number'    =>$request->account_number,
                        'account_currency'  =>$request->account_currency,
                        'swift_code'        =>$request->swift_code,
                        'beneficiary_number'=>$request->benificiary_name,
                        'invoice_number'    =>$request->invoice_number,
                        'note'              =>$request->note,
                        'payment_method_code' =>$request->payment_method,
                        'exchange_rate'     =>$request->exchange_rate,
                        'created_at'          =>Carbon::now(),
                    ]);
                    Requester::where('req_recid', $request->req_recid)->update([
                        'subject'      => $request->invoice_number,
                        'created_at'         =>Carbon::now(),
                        'req_date'           => Carbon::now()->toDayDateTimeString(),
                    ]);
                    Tasklist::where('req_recid', $request->req_recid)->update([
                        'created_at'         =>Carbon::now(),
                        'req_date'           => Carbon::now()->toDayDateTimeString(),
                    ]);
                    Documentupload::where('req_recid', $request->req_recid)->update([
                        'created_at'         =>Carbon::now(),
                        'activity_datetime'           => Carbon::now()->toDayDateTimeString(),
                    ]);

                    /** process with upload  */
                    $attach_remove = $request->att_remove;
                    if (!empty($attach_remove)) {
                        $att_delete = explode(',', $attach_remove);
                        Documentupload::whereIn('id', $att_delete)->delete();
                    }

                    if ($request->hasFile('fileupload')) {
                        $currentUser = Auth::user();
                        $req_recid = $JournalVoucher->req_recid;
                        if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                            File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                        }
                        $destinationPath = storage_path() . '/uploads/' . $req_recid . '/';
                        $destinationPath_db = '/uploads/' . $req_recid . '/';

                        $date_time      = Carbon::now()->toDayDateTimeString();
                        foreach ($request->fileupload as $photo) {
                            $file_name = $photo->getClientOriginalName();
                            $photo->move($destinationPath, $file_name);

                            Documentupload::create([
                                'req_recid'         => $req_recid,
                                'filename'          => $file_name,
                                'filepath'          => $destinationPath_db . $file_name,
                                'doer_email'        => $currentUser->email,
                                'doer_name'         => "{$currentUser->firstname} {$currentUser->lastname}",
                                'activity_form'     => FormTypeEnum::BankPaymentVourcherRequest(),
                                'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                                'activity_datetime' => $date_time,
                            ]);
                        }
                    }

                    /** process approval */
                    $JournalVoucher->saveApprovalLevel($request->req_recid, $request->first_reviewer, $request->approver);

                    /** save log */
                    $JournalVoucher->saveLog($request->comment);

                    return $JournalVoucher;
                });

                /** send email */
                if ($success) {
                    $JournalVoucher->sendEmailToPendingUser($request->comment);
                    $JournalVoucher->blockAllReference();
                    $JournalVoucher->refreshReference();
                }

                return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($JournalVoucher->req_recid . '___no'));
            } catch (Exception $e) {
                Log::info($e);
                Session::flash('error', "Cannot submit this request . Please contact administrator.");
                return redirect()->back();
            }
        }
    }

    public function delete(Request $request)
    {
        DB::transaction(function () use ($request) {
            $bankPaymentVoucher = JournalVoucher::firstWhere('req_recid', $request->req_recid);
            Documentupload::where('req_recid', $request->req_recid)->delete();
            Tasklist::where('req_recid', $request->req_recid)->delete();
            Reviewapprove::where('req_recid', $request->req_recid)->delete();
            Requester::where('req_recid', $request->req_recid)->delete();
            JournalVourcherDetail::where('req_recid', $request->req_recid)->delete();
            JournalVoucher::where('req_recid', $request->req_recid)->delete();
            $bankPaymentVoucher->freeReferences();
            
        });
        return Redirect::to('form/journal-vouchers');
    }

    public function addNewItem(Request $request){
        try {
            $glCode = GeneralLedgerCode::firstWhere('account_number', $request->item_gl_code);
            if ($glCode) {
                $journalPayment = JournalVoucher::firstWhere('req_recid', $request->item_req_recid);
                $lcyAmount = $request->amount;
                if ($request->update_currency == 'KHR') {
                    $exchangeRate = $journalPayment->exchange_rate;
                    $amount = $request->amount;
                    $lcyAmount = $amount/$exchangeRate;
                }

                JournalVourcherDetail::create([
                    'req_recid' => $request->item_req_recid,
                    'gl_code'   => $glCode->account_number,
                    'account_name' => $glCode->account_name,
                    'branch_code'  => $request->item_branch_code,
                    'currency'     => $request->currency,
                    'dr_cr'        => $request->dr_cr,
                    'amount'       => $request->amount,
                    'lcy_amount'   => $lcyAmount,
                    'budget_code'  => $request->item_budget_code,
                    'al_budget_code'  => $request->item_al_budget_code,
                    'tax_code'     => $request->item_tax_code,
                    'supp_code'    => $request->item_supplier_code,
                    'department_code' => $request->item_department_code,
                    'product_code'    => $request->item_product_code,
                    'segment_code'    => $request->item_segment_code,
                    'naratives'       => $request->item_narative,
                ]);
            }


            if ($journalPayment) {
                $journalPayment->updateTotalAmount();
            }

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot create new item please contact your administator.");
            return redirect()->back();
        }
    }
    public function updateItem(Request $request)
    {
        try {

            /** update advance item */
            $journalDetail = JournalVourcherDetail::firstWhere('id', $request->update_item_id);
            if (!$journalDetail) {
                Session::flash('error', "Cannot update, Item not found for id: {$request->update_item_id}");
                return redirect()->back();
            }

            /** check id user want to update or delete that item */
            if ($request->activity == 'update_item') {
                $glCode = GeneralLedgerCode::firstWhere('account_number', $request->update_gl_code);
                if ($glCode) {
                    $lcyAmount = $request->update_amount;
                    if ($request->update_currency == 'KHR') {
                        $journalPayment =  JournalVoucher::firstWhere('req_recid', $journalDetail->req_recid);
                        $exchangeRate = $journalPayment->exchange_rate;
                        $amount = $request->update_amount;
                        $lcyAmount = $amount/$exchangeRate;
                    }

                    $journalDetail->update([
                        'gl_code'   => $glCode->account_number,
                        'account_name' => $glCode->account_name,
                        'branch_code'  => $request->update_branch_code,
                        'currency'     => $request->update_currency,
                        'dr_cr'        => $request->update_dr_cr,
                        'amount'       => $request->update_amount,
                        'lcy_amount'   => $lcyAmount,
                        'budget_code'  => $request->update_budget_code,
                        'al_budget_code'  => $request->update_al_budget_code,
                        'tax_code'     => $request->update_tax_code,
                        'supp_code'    => $request->update_supplier_code,
                        'department_code' => $request->update_department_code,
                        'product_code'    => $request->update_product_code,
                        'segment_code'    => $request->update_segment_code,
                        'naratives'       => $request->update_narative,
                     ]);
                }

                $journalvoucher = JournalVoucher::firstWhere('req_recid', $request->update_req_recid);
                if ($journalvoucher) {
                    $journalvoucher->updateTotalAmount();
                }

                return redirect()->back();
            }


            if ($request->activity == 'delete_item') {

                /**check if current item is only one, if it is only one, we need to delete all reqeust form */
                JournalVourcherDetail::firstWhere('id', $request->update_item_id)->delete();

                /** check if item all delted */
                $JournalVourcherDetail = JournalVourcherDetail::firstWhere('req_recid', $request->update_req_recid);
                if (!$JournalVourcherDetail) {
                    DB::transaction(function () use ($request) {
                        Documentupload::where('req_recid', $request->update_req_recid)->delete();
                        Tasklist::where('req_recid', $request->update_req_recid)->delete();
                        Reviewapprove::where('req_recid', $request->update_req_recid)->delete();
                        Requester::where('req_recid', $request->update_req_recid)->delete();
                        JournalVourcherDetail::where('req_recid', $request->update_req_recid)->delete();
                        JournalVoucher::where('req_recid', $request->update_req_recid)->delete();
                    });
                    return Redirect::to('form/journal-vouchers');
                }

                $journal = JournalVoucher::firstWhere('req_recid', $request->update_req_recid);
                if ($journal) {
                    $journal->updateTotalAmount();
                }
            }

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function updateExchangeRate(Request $request)
    {
        /**@var JournalVoucher $journalVoucher */
        $journalVoucher = JournalVoucher::firstWhere('req_recid', $request->req_recid);
        if (!$journalVoucher) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }
        $journalVoucher->update([
            'exchange_rate'     =>$request->exchange_rate,
        ]);

        return redirect()->back();
    }
    public function detail($cryptedString)
    {
        $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

        /**@var JournalVoucher $journal */
        $journal = JournalVoucher::firstWhere('req_recid', $req_recid);
        if (!$journal) {
            Session::flash('error', 'Form not found!');
            return redirect()->back();
        }


        $journalDetails = JournalVourcherDetail::where('req_recid', $req_recid)->get();

        /** find document uploads */
        $documents = Documentupload::where('req_recid', $req_recid)->get();
        $totalDocument = collect($documents)->count();

        /** find request log */
        $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('doer_role', 'doer_action','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

        /** find requester */
        $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

        /** find approvers */
        $approvalUsers = $journal->getUserApprovalLevel();
        $pendingUser = $tasklist->getPendingUser();
        $user = Auth::user();

        $totalDRCR = $journal->getTotalDRCR();

        /**find is cross currency */
        $currencies = collect($journalDetails)->pluck('currency');
        $isCrossCurrency = $journal->isCrossCurrency($currencies);
        $defaultCurrency = 'USD';
        if (!$isCrossCurrency) {
            $defaultCurrency = $currencies[0];
        }
        $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
        $al_budgetcode_na = $journal->getBudgetNA($al_budget_codes);
        $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
        $budget_codes = collect($journalDetails)->pluck('budget_code');
        $budgetcode_na = $journal->getBudgetNA($budget_codes);
        $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();

        /** find request id */
        $contains = Str::contains($journal->ref_no , [',']);
        $rp_ref_no_advance=array();
        // multi request
        if($contains == true){
            $merge_req = explode(',',$journal->ref_no,10);
            foreach($merge_req as $req){
                $type_form = substr($req,0,2);
                $type_form_ADV = substr($req,0,3);
                $cryp_advance = Crypt::encrypt($req . '___no');
                if($type_form === "RP"){
                    $url_advance  = url("form/payment/detail/{$cryp_advance}");
                }elseif($type_form === "PR"){
                    $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                }elseif($type_form_ADV === "ADV"){
                    $url_advance  = url("form/advances/detail/{$cryp_advance}");
                }else{
                    $url_advance  = url("form/clear-advances/detail/{$cryp_advance}");
                }
                $rp_ref_no = [
                        'href' => $url_advance,
                        'value' => $req
                ];
                $rp_ref_no_advance[] = $rp_ref_no;
            }

            
        }else{
            $string_req = $journal->ref_no;
            $type_form = substr($string_req,0,2);
            $type_form_ADV = substr($string_req,0,3);
            $cryp_advance = Crypt::encrypt($string_req . '___no');
            if($type_form === "RP"){
                $url_advance  = url("form/payment/detail/{$cryp_advance}");
            }elseif($type_form === "PR"){
                $url_advance  = url("form/procurement/detail/{$cryp_advance}");
            }elseif($type_form_ADV === "ADV"){
                $url_advance  = url("form/advances/detail/{$cryp_advance}");
            }else{
                $url_advance  = url("form/clear-advances/detail/{$cryp_advance}");
            }
            $rp_ref_no_advance[] = [
                'href' => $url_advance,
                'value' => $string_req
            ];
        }
        return view('accounting_voucher.journal.detail', compact(
            'rp_ref_no_advance',
            'journal',
            'journalDetails',
            'documents',
            'totalDocument',
            'auditlogs',
            'tasklist',
            'pendingUser',
            'user',
            'approvalUsers',
            'totalDRCR',
            'defaultCurrency',
            'totalAndYTD',
            'totalAndYTDAL',
            'al_budgetcode_na',
            'budgetcode_na'
        ));
    }
    public function exportFormToPDF($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var JournalVoucher $journal */
            $journal = JournalVoucher::firstWhere('req_recid', $req_recid);
            if (!$journal) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            $journalDetails = JournalVourcherDetail::where('req_recid', $req_recid)->get();

            /** find approver log */
            $preparedBy = $journal->findPreparedByUser();
            $firstReviewer = $journal->findFirstReviewer();
            $approver = $journal->findApprover();
            $paidBy = $journal->findPaidBy();

            $totalDRCR = $journal->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($journalDetails)->pluck('currency');
            $isCrossCurrency = $journal->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
            $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
            $al_budgetcode_na = $journal->getBudgetNA($al_budget_codes);
            $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budget_codes = collect($journalDetails)->pluck('budget_code');
            $budgetcode_na = $journal->getBudgetNA($budget_codes);
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('doer_role', 'doer_action','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $merge_req = explode(',',$journal->ref_no,10);
            $pdf = PDF::loadView('accounting_voucher.journal.exportPDF', compact(
                'merge_req',
                'journal',
                'journalDetails',
                'preparedBy',
                'firstReviewer',
                'approver',
                'paidBy',
                'totalDRCR',
                'defaultCurrency',
                'totalAndYTD',
                'totalAndYTDAL',
                'auditlogs',
                'documents',
                'budgetcode_na',
                'al_budgetcode_na'
            ));
            return $pdf->download($req_recid.'.pdf');
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function showForApproval($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**check if form assign back then redirect to resubmit form */
            /**@var Tasklist $tasklist */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                        ->select('tasklist.*', 'recordstatus.record_status_description')
                        ->where('req_recid', $req_recid)
                        ->first();
            $approval_change_status = $tasklist->change_status_request_to;
            if ($tasklist->isAssignedBack()) {
                return Redirect::to('form/journal-vouchers/show-for-resubmitting/' . Crypt::encrypt($req_recid . '___no'));
            }

            if ($tasklist->isQuery()) {
                return Redirect::to('form/journal-vouchers/show-for-query/' . Crypt::encrypt($req_recid . '___no'));
            }

            /**@var JournalVoucher $journal */
            $journal = JournalVoucher::firstWhere('req_recid', $req_recid);
            if (!$journal) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }


            $journalDetails = journalVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('doer_role', 'doer_action','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $journal->getUserApprovalLevel();
            $pendingUser = $tasklist->getPendingUser();
            $user = Auth::user();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }

            /**
             * this case is focus on accounting level as last approver to
             * select payment method to send email and complete this request
             * Accounting team can write down the content of email that they want to
             */
             $isRequirToSelectPaymentMethod = false;
             $emailContent = '';
             $paymentMethodGroupIds ='';
             if ($pendingLevel && $pendingLevel->checker == 'accounting_voucher') {
                 $isRequirToSelectPaymentMethod = true;
             }

            $totalDRCR = $journal->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($journalDetails)->pluck('currency');
            $isCrossCurrency = $journal->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
            $paymentMethods     = PaymentMethod::get();
            $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
            $al_budgetcode_na = $journal->getBudgetNA($al_budget_codes);
            $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','total','budget_item','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budget_codes = collect($journalDetails)->pluck('budget_code');
            $budgetcode_na = $journal->getBudgetNA($budget_codes);
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            /** find request id */
            $contains = Str::contains($journal->ref_no , [',']);
            $rp_ref_no_advance=array();
            // multi request
            if($contains == true){
                $merge_req = explode(',',$journal->ref_no,10);
                foreach($merge_req as $req){
                    $type_form = substr($req,0,2);
                    $type_form_ADV = substr($req,0,3);
                    $cryp_advance = Crypt::encrypt($req . '___no');
                    if($type_form === "RP"){
                        $url_advance  = url("form/payment/detail/{$cryp_advance}");
                    }elseif($type_form === "PR"){
                        $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                    }elseif($type_form_ADV === "ADV"){
                        $url_advance  = url("form/advances/detail/{$cryp_advance}");
                    }else{
                        $url_advance  = url("form/clear-advances/detail/{$cryp_advance}");
                    }
                    $rp_ref_no = [
                            'href' => $url_advance,
                            'value' => $req
                    ];
                    $rp_ref_no_advance[] = $rp_ref_no;
                }

                
            }else{
                $string_req = $journal->ref_no;
                $type_form = substr($string_req,0,2);
                $type_form_ADV = substr($string_req,0,3);
                $cryp_advance = Crypt::encrypt($string_req . '___no');
                if($type_form === "RP"){
                    $url_advance  = url("form/payment/detail/{$cryp_advance}");
                }elseif($type_form === "PR"){
                    $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                }elseif($type_form_ADV === "ADV"){
                    $url_advance  = url("form/advances/detail/{$cryp_advance}");
                }else{
                    $url_advance  = url("form/clear-advances/detail/{$cryp_advance}");
                }
                $rp_ref_no_advance[] = [
                    'href' => $url_advance,
                    'value' => $string_req
                ];
            }
            return view('accounting_voucher.journal.approval', compact(
                'rp_ref_no_advance',
                'journal',
                'journalDetails',
                'documents',
                'totalDocument',
                'auditlogs',
                'tasklist',
                'pendingUser',
                'user',
                'approvalUsers',
                'isApprover',
                'isRequirToSelectPaymentMethod',
                'totalDRCR',
                'defaultCurrency',
                'paymentMethods',
                'totalAndYTD',
                'totalAndYTDAL',
                'al_budgetcode_na',
                'budgetcode_na',
                'approval_change_status'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function resubmitForm(Request $request)
    {
        /**@var JournalVoucher $journalVoucher */
        $journalVoucher = JournalVoucher::firstWhere('req_recid', $request->req_recid);
        if (!$journalVoucher) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }

        /**@var User $user */
        $user = Auth::user();
        if (!$user->isBelongJournalVoucherForm($journalVoucher)) {
            Session::flash('error', "Make sure you are requester to submit.");
            return redirect()->back();
        }
        /** check if user has permission to submit request */
        try {
            $success = DB::transaction(function () use ($journalVoucher, $request) {
                $references = '';
                if ($request->has('references')) {
                    $references = collect($request->references)->implode(',');
                }

                $journalVoucher->update([
                    'ref_no'            =>$references,
                    'voucher_number'    =>$request->voucher_number,
                    'department'        =>$request->department,
                    'request_date'      =>$request->request_date,
                    'currency'          =>$request->currency,
                    'bank_name'         =>$request->bank_name,
                    'account_name'      =>$request->account_name,
                    'account_number'    =>$request->account_number,
                    'account_currency'  =>$request->account_currency,
                    'swift_code'        =>$request->swift_code,
                    'beneficiary_number'=>$request->benificiary_name,
                    'invoice_number'    =>$request->invoice_number,
                    'note'              =>$request->note,
                    'payment_method_code' =>$request->payment_method,
                ]);
                Requester::where('req_recid', $request->req_recid)->update([
                    'subject'      => $request->invoice_number,
                ]);

                $currentUser = Auth::user();

                /** process with upload  */
                $attach_remove = $request->att_remove;
                if (!empty($attach_remove)) {
                    $att_delete = explode(',', $attach_remove);
                    Documentupload::whereIn('id', $att_delete)->delete();
                }

                if ($request->hasFile('fileupload')) {
                    $req_recid = $journalVoucher->req_recid;
                    if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                        File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                    }
                    $destinationPath = storage_path() . '/uploads/' . $req_recid . '/';
                    $destinationPath_db = '/uploads/' . $req_recid . '/';

                    $date_time      = Carbon::now()->toDayDateTimeString();
                    foreach ($request->fileupload as $photo) {
                        $file_name = $photo->getClientOriginalName();
                        $photo->move($destinationPath, $file_name);

                        Documentupload::create([
                            'req_recid'         => $req_recid,
                            'filename'          => $file_name,
                            'filepath'          => $destinationPath_db . $file_name,
                            'doer_email'        => $currentUser->email,
                            'doer_name'         => "{$currentUser->firstname} {$currentUser->lastname}",
                            'activity_form'     => FormTypeEnum::JournalVourcherRequest(),
                            'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                            'activity_datetime' => $date_time,
                        ]);
                    }
                }

                /** update task list */
                /**@var Tasklist $taskList*/
                $taskList = Tasklist::firstWhere('req_recid', $journalVoucher->req_recid);
                Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
                    'next_checker_group' => $taskList->assign_back_by,
                    'next_checker_role'  => $taskList->by_role,
                    'step_number'        => $taskList->by_step,
                    'within_budget'      => $taskList->within_budget,
                    'assign_back_by'     => null,
                    'by_step'            => null,
                    'by_role'            => null,
                    'req_status'         => '002'
                ]);

                /** create audit log */
                Auditlog::create([
                    'req_recid'            => $journalVoucher->req_recid,
                    'doer_email'           => $currentUser->email,
                    'doer_name'            => "{$currentUser->firstname} {$currentUser->lastname}",
                    'doer_branch'          => $currentUser->department,
                    'doer_position'        => $currentUser->position,
                    'activity_code'        => ActivityCodeEnum::Resubmitted(),
                    'activity_description' => $request->comment,
                    'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString()
                ]);

                return $journalVoucher;
            });

            /** send email */
            if ($success) {
                $journalVoucher->sendEmailToPendingUser($request->comment);

                /** block all references form request */
                $journalVoucher->blockAllReference();
                $journalVoucher->refreshReference();
            }

            return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($journalVoucher->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot submit this request . Please contact administrator.");
            return redirect()->back();
        }
    }

    public function showForQuery($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var JournalVoucher $journal */
            $journal = JournalVoucher::firstWhere('req_recid', $req_recid);
            if (!$journal) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }


            $journalDetails = JournalVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('doer_role', 'doer_action','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $journal->getUserApprovalLevel();
            $pendingUser = $tasklist->getPendingUser();
            $user = Auth::user();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }

            $totalDRCR = $journal->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($journalDetails)->pluck('currency');
            $isCrossCurrency = $journal->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
            $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
            $al_budgetcode_na = $journal->getBudgetNA($al_budget_codes);
            $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budget_codes = collect($journalDetails)->pluck('budget_code');
            $budgetcode_na = $journal->getBudgetNA($budget_codes);
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            return view('accounting_voucher.journal.query', compact(
                'journal',
                'journalDetails',
                'documents',
                'totalDocument',
                'auditlogs',
                'tasklist',
                'pendingUser',
                'user',
                'approvalUsers',
                'isApprover',
                'totalDRCR',
                'defaultCurrency',
                'totalAndYTD',
                'totalAndYTDAL',
                'budgetcode_na',
                'al_budgetcode_na'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function queryBackToApprover(Request $request)
    {
        try {
            /** make sure curren trequest is need to be query */
            $taskList =  Tasklist::firstWhere('req_recid', $request->req_recid);
            if (!$taskList->isQuery()) {
                return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($request->req_recid . '___no'));
            }

            DB::transaction(function () use ($request) {
                Tasklist::where('req_recid', $request->req_recid)
                ->update([
                    'req_status' => RequestStatusEnum::Pending()
                ]);

                /** log current user approve this procurement  */
                $user = Auth::user();
                Auditlog::create([
                    'req_recid'            => $request->req_recid,
                    'doer_email'           => $user->email,
                    'doer_name'            => "{$user->firstname} {$user->lastname}",
                    'doer_branch'          => $user->department,
                    'doer_position'        => $user->position,
                    'activity_code'        => ActivityCodeEnum::Query(),
                    'activity_description' => $request->comment,
                    'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString()
                ]);
            });

            return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($request->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot query back this request . Please contact administrator.");
            return redirect()->back();
        }
    }

    public function getRequestInfo($cryptedString, $references)
    {
        $req_recid =  $references;

        /** check if it is payment */
        $payment = Payment::firstWhere('req_recid', $req_recid);
        if ($payment) {
            return response()->json([
                'bank_name'=>$payment->bank_name,
                'account_name'=>$payment->account_name,
                'account_number'=>$payment->account_number,
            ]);
        }

        /** check if it is procurement */
        $procurement = Procurement::firstWhere('req_recid', $req_recid);
        if ($procurement) {
            return response()->json([
                'bank_name'=>'',
                'account_name'=>'',
                'account_number'=>'',
            ]);
        }

        /** check if it is advance */
        $advance = AdvanceForm::firstWhere('req_recid', $req_recid);
        if ($advance) {
            return response()->json([
                'bank_name'=>$advance->bank_name,
                'account_name'=>$advance->account_name,
                'account_number'=>$advance->account_number,
            ]);
        }

        /** check if it is clear advance */
        $clearAdvance = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
        if ($clearAdvance) {
            return response()->json([
                'bank_name'=>$clearAdvance->bank_name,
                'account_name'=>$clearAdvance->account_name,
                'account_number'=>$clearAdvance->account_number,
            ]);
        }

        return response()->json([
            'bank_name'=>'',
            'account_name'=>'',
            'account_number'=>'',
        ]);
    }

    public function showReSubmitForm($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var JournalVoucher $journal */
            $journal = JournalVoucher::firstWhere('req_recid', $req_recid);
            if (!$journal) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find references */
            $choosedReferences = [];
            if ($journal->ref_no) {
                $requestIds = explode(',', $journal->ref_no);
                if (count($requestIds) > 0) {
                    $choosedReferences = $requestIds;
                }
            }
            $originalReferences =  ViewJournalVoucherReference::get();
            $arrReferences = collect($originalReferences)->map(function ($request) {
                return $request->req_recid;
            })->toArray();
            $references = array_diff($arrReferences, $choosedReferences);


            $journalDetails = JournalVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('doer_role', 'doer_action','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $journal->getUserApprovalLevel();
            $pendingUser = $tasklist->getPendingUser();
            $user = Auth::user();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }


            /**if total debit not equal total credit then not allow to view approver */
            $totalDRCR = $journal->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($journalDetails)->pluck('currency');
            $isCrossCurrency = $journal->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }

            $generalLedgerCodes = GeneralLedgerCode::orderBy('account_number', 'desc')->get();
            $brancheCodes       = RealBranch::orderBy('code', 'desc')->get();
            $budgetCodes        = Budgetcode::orderByRaw("CASE
                                                            WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                                ELSE 1
                                                            END")
                                                    ->orderBy('budget_code', 'desc')
                                                    ->get();    
            $taxCodes           = TAXCode::orderBy('code', 'desc')->get();
            $supplierCodes      = Supplier::orderBy('code', 'desc')->get();
            $departmentCodes    = Branchcode::orderBy('branch_code', 'desc')->get();
            $productCodes       = ProductCode::orderBy('code', 'desc')->get();
            $segmentCodes       = SegmentCode::orderBy('code', 'desc')->get();
            $paymentMethods     = PaymentMethod::get();

            $al_budget_codes = collect($journalDetails)->pluck('al_budget_code');
            $al_budgetcode_na = $journal->getBudgetNA($al_budget_codes);
            $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budget_codes = collect($journalDetails)->pluck('budget_code');
            $budgetcode_na = $journal->getBudgetNA($budget_codes);
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();

            return view('accounting_voucher.journal.resubmit', compact(
                'journal',
                'journalDetails',
                'documents',
                'totalDocument',
                'auditlogs',
                'tasklist',
                'pendingUser',
                'user',
                'approvalUsers',
                'isApprover',
                'generalLedgerCodes',
                'brancheCodes',
                'budgetCodes',
                'taxCodes',
                'supplierCodes',
                'departmentCodes',
                'productCodes',
                'segmentCodes',
                'paymentMethods',
                'references',
                'choosedReferences',
                'totalDRCR',
                'defaultCurrency',
                'totalAndYTD',
                'totalAndYTDAL',
                'budgetcode_na',
                'al_budgetcode_na'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function approveRequest(Request $request)
    {
        try {
            /**@var JournalVoucher $journalVoucher */
            $journalVoucher = JournalVoucher::firstWhere('req_recid', $request->req_recid);
            if (!$journalVoucher) {
                Session::flash('error', "We cannot find this request please contact administor.");
                return redirect()->back();
            }

            /** make sure that this advance form is pending to current user */
            /**@var User $user */
            $user = Auth::user();
            if (!$journalVoucher->isPendingOnUser($user)) {
                return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($journalVoucher->req_recid . '___no'));
            }
            //**check role and action for user */
            $request_status = Tasklist::firstWhere('req_recid', $request->req_recid);
            if($request_status->step_number == 1){
                $doerRole = 'Reviewer';
                $doerAction = 'Reviewed';
            }elseif ($request_status->step_number == 2){
                $doerRole = 'Approver';
                $doerAction = 'Approved';
            }elseif ($request_status->step_number == 3){
                $doerRole = 'Accounting';
                $doerAction = 'Confirmed Posting';
            }else{
                $doerRole = 'Reviewer';
                $doerAction = 'Reviewed';
            }
            $success = DB::transaction(function () use ($request, $journalVoucher, $user, $doerRole, $doerAction) {

                /** process approval */
                if ($request->activity == 'approve') {
                  JournalVoucher::where('req_recid', $request->req_recid)->update([
                      'batch_number' =>  $request->batch_no
                  ]);
                    $user->approveJournalVoucherForm($journalVoucher, $request->comment, $doerRole, $doerAction);
                }

                /** process reject */
                if ($request->activity == 'reject') {
                    $user->rejectJournalVoucherForm($journalVoucher, $request->comment, $doerRole, 'Rejected');

                    /** free reference */
                    $journalVoucher->freeReferences();
                }

                /** process asssign back */
                if ($request->activity == 'assign_back') {
                    $user->assignJournalVoucherFormBack($journalVoucher, $request->comment, $doerRole, 'Assigned Back');
                }

                /**process query */
                if ($request->activity == 'query') {
                    $user->queryJournalVoucherForm($journalVoucher, $request->comment, $doerRole, 'Queried Back');
                }
                /**process authorize */
                if($request->activity == 'authorize'){
                    $user->authorizeJournalVoucherForm($journalVoucher, $request->comment);
                } 

                return $journalVoucher;
            });

            /**send email */
            if ($success) {
                if ($request->activity == 'send_email') {
                    $journalVoucher->sendEmailToPaymentMethodFor([
                        'payment_method_code'=>$request->new_payment_method,
                        'payment_method_email_content'=>$request->email_conten,
                    ]);
                    return Redirect::to('form/journal-vouchers/show-for-approval/' . Crypt::encrypt($journalVoucher->req_recid . '___no'));
                }

                if ($request->activity == 'approve') {
                    $journalVoucher->sendEmailFormHasBeenApproved($request->comment);
                }

                if ($request->activity == 'reject') {
                    $journalVoucher->sendEmailFormHasBeenRejected($request->comment);
                }

                if ($request->activity == 'assign_back') {
                    $journalVoucher->sendEmailFormHasBeenAssignedBack($request->comment);
                }

                if ($request->activity == 'query') {
                    $journalVoucher->sendEmailFormHasBeenQueriedBack($request->comment);
                }
            }

            return Redirect::to('form/journal-vouchers/detail/' . Crypt::encrypt($journalVoucher->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Please contact administor.");
            return redirect()->back();
        }
    }
    public function removeattach($attachid){
        $id=$attachid;
        $document=Documentupload::find($id);
        $success = $document->delete();
        return response()->json(['data'=>'success']);
    }
    public function updateStatus(Request $request){
        try {
            $req_recid = $request->recid;
            $comment = $request->comment;
            $type_request = $request->type_accounting_voucher;
            /**@var User $user */
            $user = Auth::user();
            $user->changeStatusRequest($req_recid,$comment,$type_request);
            Session::flash('success', 'Your request was change!');
            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function exportFormToExcel($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var JournalVoucher $journalVouhcer */
            $journalVouhcer = JournalVoucher::firstWhere('req_recid', $req_recid);
            if (!$journalVouhcer) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }
            return Excel::download(new ExportJournals($req_recid), 'Journal'.date('F, Y').'.xlsx');
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

}
