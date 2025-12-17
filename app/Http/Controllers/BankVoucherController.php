<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Models\AdvanceForm;
use App\Models\Auditlog;
use App\Models\BankReceiptVoucher;
use App\Models\BankVourcherDetail;
use App\Models\Branchcode;
use App\Models\Budgetcode;
use App\Models\BankVoucher;
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
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Reviewapprove;
use App\Models\ViewBankReceiptVoucherReference;
use App\Traits\Currency;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;
use App\Models\BudgetDetail;
use App\Models\Currencise;
use Illuminate\Support\Str;

class BankVoucherController extends Controller
{
    use Currency;
    public function index(){
        $user   = Auth::user();
        $result = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', FormTypeEnum::BankVourcherRequest())
            ->where('tasklist.req_email', $user->email)
            ->where('tasklist.req_status', RequestStatusEnum::Save())
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('treasury_voucher.bank.index', compact('result'));

    }
    public function create(){
        $generalLedgerCodes = GeneralLedgerCode::get();
        $brancheCodes       = RealBranch::get();
        $supplierCodes      = Supplier::get();
        $departmentCodes    = Branchcode::get();
        $segmentCodes       = SegmentCode::get();
        $budgetCodes        = BudgetCode::get();
        $currency           = Currencise::get();

        /** current user information */
        $user = Auth::user();
        return view('treasury_voucher.bank.create', compact(
            'generalLedgerCodes',
            'brancheCodes',
            'supplierCodes',
            'departmentCodes',
            'segmentCodes',
            'user',
            'budgetCodes',
            'currency'
        ));
    }
    public function downloadExcel()
    {
        $path = public_path('/static/template/bank-voucher-template.xlsx');
        return response()->download($path);
    }
    public function saveDraft(Request $request)
    {
        try {
            $totalDRCR = $this->calculateDRCR([
                'amounts'   => $request->amounts,
                'currencies'=> $request->currencies,
                'dr_crs'    => $request->dr_crs,
                'exchange_rate' => $request->exchange_rate,
                'eur_exchange_rate' => $request->eur_exchange_rate,
                'thb_exchange_rate' => $request->thb_exchange_rate,
            ]);

            if ($totalDRCR->totalDR != $totalDRCR->totalCR) {
                Session::flash('error', "TOTAL CREDIT must be equalt to TOTAL DEBIT");
                return redirect()->back();
            }
            // $multiCurrency = collect($request->currencies)->unique()->count();
            // if($multiCurrency > 2){
            //     Session::flash('error', "CAN NOT ADD 3 CURRENCIES TO REQUEST!");
            //     return redirect()->back();
            // }

            /** if it is not cross currency then exhcange rate need to set to 1 */
            $exchangeRate = 1;
            $exchangeRateTHB = 1;
            $exchangeRateEUR = 1;
            if ($this->isCrossCurrency($request->currencies)) {
                $exchangeRate = $request->exchange_rate;
                $exchangeRateTHB = $request->thb_exchange_rate;
                $exchangeRateEUR = $request->eur_exchange_rate;
            }
            /**@var BankVoucher $bank */
            $bank = BankVoucher::create([
                'voucher_date'              =>$request->voucher_date,
                'department'                =>$request->department,
                'batch_number'              =>$request->batch_number,
                'request_date'              =>$request->request_date,
                'currency'                  =>$request->currency,
                'exchange_rate'             =>$exchangeRate,
                'thb_exchange_rate'         =>$exchangeRateTHB,
                'eur_exchange_rate'         =>$exchangeRateEUR,
                'description'               =>$request->description,
                'note'                      =>$request->note,
            ]);
            $bank->refresh();

            $success = DB::transaction(function () use ($bank, $request) {

            /** create details */
                $bank->createDetails([
                'gl_codes'          =>  $request->gl_codes,
                'branch_codes'      =>  $request->branch_codes,
                'amounts'           =>  $request->amounts,
                'currencies'        =>  $request->currencies,
                'budget_codes'      =>  $request->budget_codes,
                'dr_crs'            =>  $request->dr_crs,
                'exchange_rate'     =>  $request->exchange_rate,
                'thb_exchange_rate' =>  $request->thb_exchange_rate,
                'eur_exchange_rate' =>  $request->eur_exchange_rate,
                'supp_codes'        =>  $request->supp_codes,
                'dept_codes'        =>  $request->dept_codes,
                'descriptions'      =>  $request->descriptions,
           ]);

                /** create tasklist */
                $currentUser = Auth::user();
                $tasklist = Tasklist::create([
                'req_recid'          => $bank->req_recid,
                'req_email'          => $currentUser->email,
                'req_name'           => "{$currentUser->firstname} {$currentUser->lastname}",
                'req_branch'         => $currentUser->department,
                'req_position'       => $currentUser->position,
                'req_from'           => FormTypeEnum::BankVourcherRequest(),
                'req_type'           => FormTypeEnum::BankVourcherRequest(),
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
                'req_recid'    => $bank->req_recid,
                'req_email'    => $currentUser->email,
                'req_name'     => "{$currentUser->firstname} {$currentUser->lastname}",
                'req_branch'   => $currentUser->department,
                'req_position' => $currentUser->position,
                'req_from'     => FormTypeEnum::BankVourcherRequest(),
                'req_date'     => Carbon::now()->toDayDateTimeString(),
                'subject'      => '',
                'ccy'          => $request->currency
            ]);

                /** upload file if hase */
                if ($request->hasFile('fileupload')) {
                    $req_recid = $bank->req_recid;
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
                        'activity_form'     => FormTypeEnum::BankVourcherRequest(),
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
            $bank->updateTotalAmount();


            return Redirect::to('form/bank-vouchers/edit/' . Crypt::encrypt($bank->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please contact aministration for check your issue.');
            return redirect()->back();
        }
    }
    private function calculateDRCR($items)
    {
        $item = (object)$items;
        $amounts = $item->amounts;
        $currencies = $item->currencies;
        $dr_crs = $item->dr_crs;
        $exchangeRate = (float)$item->exchange_rate;
        $exchangeRateTHB = (float)$item->thb_exchange_rate;
        $exchangeRateEUR = (float)$item->eur_exchange_rate;

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
                    if ($currency == 'THB') {
                        $debitAmount = $amount / $exchangeRateTHB;
                    }
                    if ($currency == 'EUR') {
                        $debitAmount = $amount / $exchangeRateEUR;
                    }
                    $totalDR += $debitAmount;
                }

                if ($dr_cr == 'CREDIT') {
                    $creditAmount = $amount;
                    if ($currency == 'KHR') {
                        $creditAmount = $amount / $exchangeRate;
                    }
                    if ($currency == 'THB') {
                        $creditAmount = $amount / $exchangeRateTHB;
                    }
                    if ($currency == 'EUR') {
                        $creditAmount = $amount / $exchangeRateEUR;
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
    public function edit($cryptedString)
    {
        $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);
        /**@var BankVoucher $bank */
        $bank = BankVoucher::firstWhere('req_recid', $req_recid);
        if (!$bank) {
            Session::flash('error', 'Form not found!');
            return redirect()->back();
        }
        $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();
        $approvalUsers = [];
        $totalDRCR = $bank->getTotalDRCR();
        // dd($totalDRCR);
        if ($totalDRCR->total_DR == $totalDRCR->total_CR) {
            $approvalUsers = $bank->getAllApprovers();
        }
        /** find document uploads */
        $documents = Documentupload::where('req_recid', $req_recid)->get();
        $totalDocument = collect($documents)->count();

        $generalLedgerCodes = GeneralLedgerCode::get();
        $brancheCodes       = RealBranch::get();
        $supplierCodes      = Supplier::get();
        $departmentCodes    = Branchcode::get();
        $budgetCodes        = Budgetcode::get();
        $typeCurrencies         = Currencise::get();
        $user = Auth::user();
        $totalDRCR = $bank->getTotalDRCR();

        /**find is cross currency */
        $currencies = collect($bankDetails)->pluck('currency');
        $isCrossCurrency = $bank->isCrossCurrency($currencies);
        $defaultCurrency = 'USD';
        if (!$isCrossCurrency) {
            $defaultCurrency = $currencies[0];
        }
        return view('treasury_voucher.bank.edit', compact(
            'bank',
            'bankDetails',
            'documents',
            'totalDocument',
            'generalLedgerCodes',
            'brancheCodes',
            'supplierCodes',
            'departmentCodes',
            'user',
            'totalDRCR',
            'budgetCodes',
            'defaultCurrency',
            'approvalUsers',
            'typeCurrencies'
        ));
    }
    private function getRequestIdFromCryptedString($cryptedString)
    {
        $param_url = Crypt::decrypt($cryptedString);
        $after_split = explode('___', $param_url);
        return $after_split[0];
    }
    public function updateExchangeRate(Request $request)
    {
        /**@var BankVoucher $bankVoucher */
        $bankVoucher = BankVoucher::firstWhere('req_recid', $request->req_recid);
        if (!$bankVoucher) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }
        $bankVoucher->update([
            'exchange_rate'     =>$request->exchange_rate,
            'thb_exchange_rate'     =>$request->thb_exchange_rate,
            'eur_exchange_rate'     =>$request->eur_exchange_rate,
        ]);

        return redirect()->back();
    }
    public function addNewItem(Request $request)
    {
        try {
            $glCode = GeneralLedgerCode::firstWhere('account_number', $request->item_gl_code);
            if ($glCode) {
                $bank = BankVoucher::firstWhere('req_recid', $request->item_req_recid);
                $lcyAmount = $request->amount;
                if ($request->update_currency == 'KHR') {
                    $exchangeRate = $bank->exchange_rate;
                    $amount = $request->amount;
                    $lcyAmount = $amount/$exchangeRate;
                }
                if($request->update_currency == 'THB'){
                    $exchangeRate = $bank->thb_exchange_rate;
                    $amount = $request->amount;
                    $lcyAmount = $amount/$exchangeRate;
                }
                if($request->update_currency == 'EUR'){
                    $exchangeRate = $bank->eur_exchange_rate;
                    $amount = $request->amount;
                    $lcyAmount = $amount/$exchangeRate;
                }

                BankVourcherDetail::create([
                    'req_recid' => $request->item_req_recid,
                    'gl_code'   => $glCode->account_number,
                    'account_name' => $glCode->account_name,
                    'branch_code'  => $request->item_branch_code,
                    'currency'     => $request->currency,
                    'dr_cr'        => $request->dr_cr,
                    'amount'       => $request->amount,
                    'lcy_amount'   => $lcyAmount,
                    'budget_code'  => $request->item_budget_code,
                    'supp_code'    => $request->item_supplier_code,
                    'department_code' => $request->item_department_code,
                    'descriptions' => $request->item_description,
                ]);
            }


            if ($bank) {
                $bank->updateTotalAmount();
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

            /** update Treasury item */
            $bankDetail = BankVourcherDetail::firstWhere('id', $request->update_item_id);
            if (!$bankDetail) {
                Session::flash('error', "Cannot update, Item not found for id: {$request->update_item_id}");
                return redirect()->back();
            }

            /** check id user want to update or delete that item */
            if ($request->activity == 'update_item') {
                $glCode = GeneralLedgerCode::firstWhere('account_number', $request->update_gl_code);
                if ($glCode) {
                    $lcyAmount = $request->update_amount;
                    $bank =  BankVoucher::firstWhere('req_recid', $bankDetail->req_recid);
                    if ($request->update_currency == 'KHR') {
                        $exchangeRate = $bank->exchange_rate;
                        $amount = $request->update_amount;
                        $lcyAmount = $amount/$exchangeRate;
                    }
                    if ($request->update_currency == 'THB') {
                        $exchangeRate = $bank->thb_exchange_rate;
                        $amount = $request->update_amount;
                        $lcyAmount = $amount/$exchangeRate;
                    }
                    if ($request->update_currency == 'EUR') {
                        $exchangeRate = $bank->eur_exchange_rate;
                        $amount = $request->update_amount;
                        $lcyAmount = $amount/$exchangeRate;
                    }

                    $bankDetail->update([
                        'gl_code'   => $glCode->account_number,
                        'account_name' => $glCode->account_name,
                        'branch_code'  => $request->update_branch_code,
                        'currency'     => $request->update_currency,
                        'dr_cr'        => $request->update_dr_cr,
                        'amount'       => $request->update_amount,
                        'lcy_amount'   => $lcyAmount,
                        'budget_code'  => $request->update_budget_code,
                        'supp_code'    => $request->update_supplier_code,
                        'department_code' => $request->update_department_code,
                        'descriptions' => $request->update_descriptions,
                     ]);
                }

                $bank = BankVoucher::firstWhere('req_recid', $request->update_req_recid);
                if ($bank) {
                    $bank->updateTotalAmount();
                }

                return redirect()->back();
            }


            if ($request->activity == 'delete_item') {

                /**check if current item is only one, if it is only one, we need to delete all reqeust form */
                BankVourcherDetail::firstWhere('id', $request->update_item_id)->delete();

                /** check if item all delted */
                $bankVourcherDetail = BankVourcherDetail::firstWhere('req_recid', $request->update_req_recid);
                if (!$bankVourcherDetail) {
                    DB::transaction(function () use ($request) {
                        Documentupload::where('req_recid', $request->update_req_recid)->delete();
                        Tasklist::where('req_recid', $request->update_req_recid)->delete();
                        Reviewapprove::where('req_recid', $request->update_req_recid)->delete();
                        Requester::where('req_recid', $request->update_req_recid)->delete();
                        BankVourcherDetail::where('req_recid', $request->update_req_recid)->delete();
                        BankVoucher::where('req_recid', $request->update_req_recid)->delete();
                    });
                    return Redirect::to('form/bank-vouchers');
                }

                $bank = BankVoucher::firstWhere('req_recid', $request->update_req_recid);
                if ($bank) {
                    $bank->updateTotalAmount();
                }
            }

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function submitRequest(Request $request)
    {

        /**@var BankVoucher $bankVoucher */
        $bankVoucher = BankVoucher::firstWhere('req_recid', $request->req_recid);
        if (!$bankVoucher) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }

        /** check if form already submit */
        if ($bankVoucher->isAlreadySubmitted()) {
            return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($bankVoucher->req_recid . '___no'));
        }

        /**@var User $user */
        $user = Auth::user();
        if (!$user->isBelongToBankVoucherForm($bankVoucher)) {
            Session::flash('error', "Make sure you are requester to submit.");
            return redirect()->back();
        }
        try {
            $success = DB::transaction(function () use ($bankVoucher, $request) {
                $bankVoucher->update([
                    'voucher_date'          =>$request->voucher_date,
                    'batch_number'          =>$request->batch_number,
                    'department'        =>$request->department,
                    'request_date'      =>$request->request_date,
                    'currency'          =>$request->currency,
                    'note'              =>$request->note,
                    'description'     =>$request->description,
                ]);

                /** process with upload  */
                $attach_remove = $request->att_remove;
                if (!empty($attach_remove)) {
                    $att_delete = explode(',', $attach_remove);
                    Documentupload::whereIn('id', $att_delete)->delete();
                }

                if ($request->hasFile('fileupload')) {
                    $currentUser = Auth::user();
                    $req_recid = $bankVoucher->req_recid;
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
                            'activity_form'     => FormTypeEnum::BankVourcherRequest(),
                            'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                            'activity_datetime' => $date_time,
                        ]);
                    }
                }

                /** process approval */
                $bankVoucher->saveApprovalLevel($request->req_recid, $request->first_reviewer, $request->approver);

                /** save log */
                $bankVoucher->saveLog();

                return $bankVoucher;
            });

            /** send email */
            if ($success) {
                $bankVoucher->sendEmailToPendingUser($request->comment);
            }

            return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($bankVoucher->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot submit this request . Please contact administrator.");
            return redirect()->back();
        }
    }
    public function detail($cryptedString)
    {
        $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

        /**@var BankVoucher $bank */
        $bank = BankVoucher::firstWhere('req_recid', $req_recid);
        if (!$bank) {
            Session::flash('error', 'Form not found!');
            return redirect()->back();
        }


        $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();

        /** find document uploads */
        $documents = Documentupload::where('req_recid', $req_recid)->get();
        $totalDocument = collect($documents)->count();

        /** find request log */
        $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

        /** find requester */
        $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

        /** find approvers */
        $approvalUsers = $bank->getUserApprovalLevel();
        $pendingUser = $tasklist->getPendingUser();
        $user = Auth::user();

        $totalDRCR = $bank->getTotalDRCR();

        /**find is cross currency */
        $currencies = collect($bankDetails)->pluck('currency');
        $isCrossCurrency = $bank->isCrossCurrency($currencies);
        $defaultCurrency = 'USD';
        if (!$isCrossCurrency) {
            $defaultCurrency = $currencies[0];
        }
        return view('treasury_voucher.bank.detail', compact(
            'bank',
            'bankDetails',
            'documents',
            'totalDocument',
            'auditlogs',
            'tasklist',
            'pendingUser',
            'user',
            'approvalUsers',
            'totalDRCR',
            'defaultCurrency',
        ));
    }
    public function exportFormToPDF($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var BankVoucher $bank */
            $bank = BankVoucher::firstWhere('req_recid', $req_recid);
           
            if (!$bank) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();

            /** find approver log */
            $preparedBy = $bank->findPreparedByUser();
            $firstReviewer = $bank->findFirstReviewer();
            $approver = $bank->findApprover();

            $totalDRCR = $bank->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($bankDetails)->pluck('currency');
            $isCrossCurrency = $bank->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
           
            $budget_codes = collect($bankDetails)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $merge_req = explode(',',$bank->ref_no,10);
         
            $pdf = PDF::loadView('treasury_voucher.bank.exportPDF', compact(
                'merge_req',
                'bank',
                'bankDetails',
                'preparedBy',
                'firstReviewer',
                'approver',
                'totalDRCR',
                'defaultCurrency',
                'totalAndYTD',
                'auditlogs',
                'documents',
            ),[],['format' => 'A4-L','orientation' => 'L']);
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
            if ($tasklist->isAssignedBack()) {
                return Redirect::to('form/bank-vouchers/show-for-resubmitting/' . Crypt::encrypt($req_recid . '___no'));
            }

            if ($tasklist->isQuery()) {
                return Redirect::to('form/bank-vouchers/show-for-query/' . Crypt::encrypt($req_recid . '___no'));
            }

            /**@var BankVoucher $bank */
            $bank = BankVoucher::firstWhere('req_recid', $req_recid);
            if (!$bank) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }


            $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $bank->getUserApprovalLevel();
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
             * select  method to send email and complete this request
             * Accounting team can write down the content of email that they want to
             */
            

            $totalDRCR = $bank->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($bankDetails)->pluck('currency');
            $isCrossCurrency = $bank->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
            
            return view('treasury_voucher.bank.approval', compact(
                'bank',
                'bankDetails',
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
            /**@var BankVoucher $bankVoucher */
            $bankVoucher = BankVoucher::firstWhere('req_recid', $request->req_recid);
            if (!$bankVoucher) {
                Session::flash('error', "We cannot find this request please contact administor.");
                return redirect()->back();
            }

            /** make sure that this advance form is pending to current user */
            /**@var User $user */
            $user = Auth::user();
            if (!$bankVoucher->isPendingOnUser($user)) {
                return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($bankVoucher->req_recid . '___no'));
            }
            $success = DB::transaction(function () use ($request, $bankVoucher, $user) {

                /** process approval */
                if ($request->activity == 'approve') {
                    $user->approveBankVoucherForm($bankVoucher, $request->comment);
                }

                /** process reject */
                if ($request->activity == 'reject') {
                    $user->rejectBankVoucherForm($bankVoucher, $request->comment);
                }

                /** process asssign back */
                if ($request->activity == 'assign_back') {
                    $user->assignBankVoucherFormBack($bankVoucher, $request->comment);
                }

                /**process query */
                if ($request->activity == 'query') {
                    $user->queryBankVoucherForm($bankVoucher, $request->comment);
                }

                return $bankVoucher;
            });

            /**send email */
            if ($success) {

                if ($request->activity == 'approve') {
                    $bankVoucher->sendEmailFormHasBeenApproved($request->comment);
                }

                if ($request->activity == 'reject') {
                    $bankVoucher->sendEmailFormHasBeenRejected($request->comment);
                }

                if ($request->activity == 'assign_back') {
                    $bankVoucher->sendEmailFormHasBeenAssignedBack($request->comment);
                }

                if ($request->activity == 'query') {
                    $bankVoucher->sendEmailFormHasBeenQueriedBack($request->comment);
                }
            }

            return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($bankVoucher->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Please contact administor.");
            return redirect()->back();
        }
    }
    public function delete(Request $request)
    {
        DB::transaction(function () use ($request) {
            Documentupload::where('req_recid', $request->req_recid)->delete();
            Tasklist::where('req_recid', $request->req_recid)->delete();
            Reviewapprove::where('req_recid', $request->req_recid)->delete();
            Requester::where('req_recid', $request->req_recid)->delete();
            BankVourcherDetail::where('req_recid', $request->req_recid)->delete();
            BankVoucher::where('req_recid', $request->req_recid)->delete();
        });
        return Redirect::to('form/bank-vouchers');
    }
    public function showReSubmitForm($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var BankVoucher $bank */
            $bank = BankVoucher::firstWhere('req_recid', $req_recid);
            if (!$bank) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

           

            $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $bank->getUserApprovalLevel();
            $pendingUser = $tasklist->getPendingUser();
            $user = Auth::user();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }


            /**if total debit not equal total credit then not allow to view approver */
            $totalDRCR = $bank->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($bankDetails)->pluck('currency');
            $isCrossCurrency = $bank->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }

            $generalLedgerCodes = GeneralLedgerCode::get();
            $brancheCodes       = RealBranch::get();
            $budgetCodes        = Budgetcode::get();
            $taxCodes           = TAXCode::get();
            $supplierCodes      = Supplier::get();
            $departmentCodes    = Branchcode::get();
            $productCodes       = ProductCode::get();
            $segmentCodes       = SegmentCode::get();
            $typeCurrencies     = Currencise::get();


            return view('treasury_voucher.bank.resubmit', compact(
                'bank',
                'bankDetails',
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
                'totalDRCR',
                'defaultCurrency',
                'typeCurrencies'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function resubmitForm(Request $request)
    {
        /**@var BankVoucher $bankVoucher */
        $bankVoucher = BankVoucher::firstWhere('req_recid', $request->req_recid);
        if (!$bankVoucher) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }

        /**@var User $user */
        $user = Auth::user();
        if (!$user->isBelongToBankVoucherForm($bankVoucher)) {
            Session::flash('error', "Make sure you are requester to submit.");
            return redirect()->back();
        }
        /** check if user has permission to submit request */
        try {
            $success = DB::transaction(function () use ($bankVoucher, $request) {
                $bankVoucher->update([
                    'voucher_date'          =>$request->voucher_date,
                    'department'            =>$request->department,
                    'batch_number'          =>$request->batch_number,
                    'request_date'          =>$request->request_date,
                    'currency'              =>$request->currency,
                    'description'           =>$request->description,
                    'note'                  =>$request->note,
                ]);

                $currentUser = Auth::user();

                /** process with upload  */
                $attach_remove = $request->att_remove;
                if (!empty($attach_remove)) {
                    $att_delete = explode(',', $attach_remove);
                    Documentupload::whereIn('id', $att_delete)->delete();
                }

                if ($request->hasFile('fileupload')) {
                    $req_recid = $bankVoucher->req_recid;
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
                            'activity_form'     => FormTypeEnum::BankVourcherRequest(),
                            'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                            'activity_datetime' => $date_time,
                        ]);
                    }
                }

                /** update task list */
                /**@var Tasklist $taskList*/
                $taskList = Tasklist::firstWhere('req_recid', $bankVoucher->req_recid);
                Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
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
                    'req_recid'            => $bankVoucher->req_recid,
                    'doer_email'           => $currentUser->email,
                    'doer_name'            => "{$currentUser->firstname} {$currentUser->lastname}",
                    'doer_branch'          => $currentUser->department,
                    'doer_position'        => $currentUser->position,
                    'activity_code'        => ActivityCodeEnum::Resubmitted(),
                    'activity_description' => $request->comment,
                    'activity_form'        => FormTypeEnum::BankVourcherRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString()
                ]);

                return $bankVoucher;
            });

            /** send email */
            if ($success) {
                $bankVoucher->sendEmailToPendingUser($request->comment);
            }

            return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($bankVoucher->req_recid . '___no'));
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

            /**@var BankVoucher $bank */
            $bank = BankVoucher::firstWhere('req_recid', $req_recid);
            if (!$bank) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }


            $bankDetails = BankVourcherDetail::where('req_recid', $req_recid)->get();

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                    ->select('tasklist.*', 'recordstatus.record_status_description')
                    ->where('req_recid', $req_recid)
                    ->first();

            /** find approvers */
            $approvalUsers = $bank->getUserApprovalLevel();
            $pendingUser = $tasklist->getPendingUser();
            $user = Auth::user();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }

            $totalDRCR = $bank->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($bankDetails)->pluck('currency');
            $isCrossCurrency = $bank->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }

            return view('treasury_voucher.bank.query', compact(
                'bank',
                'bankDetails',
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
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }
    public function queryBackToApprover(Request $request){
        try {
            /** make sure curren trequest is need to be query */
            $taskList =  Tasklist::firstWhere('req_recid', $request->req_recid);
            if (!$taskList->isQuery()) {
                return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($request->req_recid . '___no'));
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
                    'activity_form'        => FormTypeEnum::BankVourcherRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString()
                ]);
            });

            return Redirect::to('form/bank-vouchers/detail/' . Crypt::encrypt($request->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot query back this request . Please contact administrator.");
            return redirect()->back();
        }
    }
    
    
}
