<?php

namespace App\Http\Controllers;

use App\Models\Branchcode;
use App\Models\Budgetcode;
use App\Models\Budgethistory;
use App\Models\Documentupload;
use App\Models\Emailgroup;
use App\Models\Flowconfig;
use App\Models\Groupid;
use App\Models\Payment;
use App\Models\Paymentbody;
use App\Models\Paymentbottom;
use App\Models\Procurementbody;
use App\Models\Procurementbottom;
use App\Models\Requester;
use App\Models\Reviewapprove;
use App\Models\Tasklist;
use App\Models\User;
use App\Models\BudgetDetail;
use App\Myclass\Defaultsave;
use App\Myclass\Sendemail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Webpatser\Uuid\Uuid;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function isWithinBudget($totalRequest, $budgets, $code, $altBudgets, $altCode)
    {
        $totalRawRemain = 0;
        $budget = collect($budgets)->firstWhere('budget_code', $code);
        if ($budget) {
            $totalRawRemain = $budget['payment_remaining'];
        }

        $altBudget = collect($altBudgets)->firstWhere('budget_code', $altCode);
        if ($altBudget) {
            $totalRawRemain = $totalRawRemain + $altBudget['payment_remaining'];
        }

        /** find within budget or not */
        $withingBudget  = 'YES';
        $amountUsed = $totalRawRemain - $totalRequest;
        if ($amountUsed < 0) {
            $withingBudget = 'NO';
        }
        return $withingBudget;
    }

    public function previewPaymentYTDExpense(Request $request)
    {
        try {
            $req_recid = $request->req_recid;
            $paymentBodies   = Paymentbody::where('req_recid', $req_recid)->orderby('id', 'asc')->get();
            $paymentHistories = Budgethistory::where('req_recid', $req_recid)->get();

            $paymentIndex    = 0;
            $paymentPreviews = [];

            /** find all budgetcodes */
            $codes    = collect($paymentHistories)->pluck('budget_code')->toArray();
            $budgetCodes = Budgetcode::whereIn('budget_code', $codes)->get();

            /** find all Alternative code */
            $altBudgetCodes = collect($paymentHistories)->pluck('alternative_budget_code')->toArray();
            $allAltBudgetCodes =  Budgetcode::whereIn('budget_code', $altBudgetCodes)->get();

            /** calculate payment preview */
            foreach ($paymentHistories as $paymentHistory) {
                try {
                    $paymentBody =  (object)$paymentBodies[$paymentIndex];
                    if (!$paymentBody) {
                        continue;
                    }
                } catch (Exception $e) {
                    continue;
                }

                $totalRequestAmount = $paymentBody->total;
                $withingBudget = $this->isWithinBudget(
                    $totalRequestAmount,
                    $budgetCodes,
                    $paymentHistory->budget_code,
                    $allAltBudgetCodes,
                    $paymentHistory->alternative_budget_code
                );


                /** update budget code payment remaining */
                $budgetCodes = collect($budgetCodes)->transform(function ($budget) use ($paymentHistory) {
                    $code = $budget;
                    if ($budget->budget_code == $paymentHistory->budget_code) {
                        $paymentRemain = $budget->payment_remaining - $paymentHistory->budget_amount_use;
                        $totalRmain = $paymentRemain > 0 ? $paymentRemain : 0;
                        $ytdExpense    =  $budget->total - $totalRmain;

                        $code['payment_remaining'] = $paymentRemain > 0 ? $paymentRemain : 0;
                        $code['ytd_exspense']      = $ytdExpense > 0 ? $ytdExpense : 0;
                    }
                    return $code;
                });


                $allAltBudgetCodes = collect($allAltBudgetCodes)->transform(function ($allAltBudgetCode) use ($paymentHistory) {
                    $code = $allAltBudgetCode;
                    if ($allAltBudgetCode->budget_code == $paymentHistory->alternative_budget_code) {
                        $paymentRemain = $allAltBudgetCode->payment_remaining - $paymentHistory->alternative_amount_use;
                        $totalRmain = $paymentRemain > 0 ? $paymentRemain : 0;
                        $ytdExpense    = $allAltBudgetCode->total - $totalRmain;

                        $code['payment_remaining'] = $paymentRemain > 0 ? $paymentRemain : 0;
                        $code['ytd_exspense']      = $ytdExpense > 0 ? $ytdExpense : 0;
                    }
                    return $code;
                });


                /** find and bind for array of budget code */
                $totalYTDExpense      = 0;
                $totalBudgetAmount    = 0;
                $totalRemainingAmount = 0;
                if ($budgetCodes->isNotEmpty()) {
                    $budgetCode = $budgetCodes->firstWhere('budget_code', $paymentHistory->budget_code);
                    if ($budgetCode) {
                        $totalYTDExpense      = $budgetCode->ytd_exspense;
                        $totalBudgetAmount    = $budgetCode->total;
                        $totalRemainingAmount =  $budgetCode->payment_remaining;
                    }
                }
                if ($allAltBudgetCodes->isNotEmpty()) {
                    $altBudgetCode = $allAltBudgetCodes->firstWhere('budget_code', $paymentHistory->alternative_budget_code);
                    if ($altBudgetCode) {
                        $altPaymentRemain     = $altBudgetCode->payment_remaining;
                        $totalYTDExpense      = $totalYTDExpense + $altBudgetCode->ytd_exspense;
                        $totalBudgetAmount    = $totalBudgetAmount + $altBudgetCode->total;
                        $totalRemainingAmount = $totalRemainingAmount + $altPaymentRemain;
                    }
                }

                /** bind item */
                array_push($paymentPreviews, [
                    'budget_code'             => $paymentHistory->budget_code,
                    'alternative_budget_code' => $paymentHistory->alternative_budget_code != 0 ? $paymentHistory->alternative_budget_code : 'N/A',
                    'total_request'           => $totalRequestAmount > 0 ? $totalRequestAmount : 0,
                    'total_budget'            => $totalBudgetAmount > 0 ? $totalBudgetAmount : 0,
                    'ytd_expense'             => $totalYTDExpense > 0 ? $totalYTDExpense : 0,
                    'total_remaining_amount'  => $totalRemainingAmount > 0 ? $totalRemainingAmount : 0,
                    'status'                  => $withingBudget
                ]);
                $paymentIndex++;
            }
            return response(['reposnseCode' => '001', 'data' => $paymentPreviews], 200);
        } catch (Exception $e) {
            Log::info($e);
        }
    }

    public function listPaymentRecord()
    {
        /**@var User $user */
        $user = Auth::user();

        /** find all allowan email */
        if (!$user->isAllowToViewPaymentRecord()) {
            return redirect()->back();
        }

        $payments = $user->getPaymentRecord();
        
        Session::put('is_allow_to_view_payment_record', true);

        return view('payment-record', compact('payments'));
    }

    public function listAuthRequest()
    {
        $user = Auth::user();
        $result = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_type', 2) //number 2 is payment request form
            ->where('tasklist.req_email', $user->email)
            ->where('tasklist.req_status', '001') //status 001 is saved/created
            ->get();

        return view('form.payment_list', compact('result'));
    }
    public function getPaymentListingData(Request $request)
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
        $user   = Auth::user();
        $record_query =  Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_type', 2)
            ->where('tasklist.req_email', $user->email)
            ->where('tasklist.req_status', '001');
        $totalRecords = $record_query->count();
        $totalRecordswithFilter = $record_query->where(function ($query) use ($searchValue) {
            $query->where('tasklist.req_recid', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_branch', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_position', 'like', '%' . $searchValue . '%');
            $query->orWhere('tasklist.req_date', 'like', '%' . $searchValue . '%');
            $query->orWhere('recordstatus.record_status_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester.subject', 'like', '%' . $searchValue . '%');
        })->count();
        $records = $record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
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
            $req_recid = '<a href="' . url($record->description . '/' . Crypt::encrypt($record->req_recid . '___' . 'no')) . '">' . $record->req_recid . '</a>';
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9" href="javascript:void(0)" style="font-weight: 400;">
            <span class="subject">' . $record->subject . '</span>
            <span class="tooltip-content5" style="width: 500px;">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body">
                        ' . $record->subject . '</span>
                    </span>
                </span>
            </span>
        </a></p>';
            $data_arr[] = array(
                "no" => $start + ($key + 1),
                "req_recid" => $req_recid,
                "subject" => $subject,
                "record_status_description" => $record->record_status_description,
                "req_branch" => $record->req_branch,
                "req_position" => $record->req_position,
                "req_date" => Carbon::parse($record->req_date)->format('d-m-Y  g:i A'),
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

    public function fromProcurement(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        $pr_id = $request->pr_id;
        $approve_pr = Tasklist::where(['req_recid' => $pr_id, 'req_type' => '1'])->first();
        if (!empty($approve_pr)) {
            if ($approve_pr->req_status == '005') {
                $pr_body = Procurementbody::join('budgetdetail', 'budgetdetail.budget_code', 'procurementbody.budget_code')
                    ->whereIn('procurementbody.req_recid', $pr_id)
                    ->where('procurementbody.paid', 'N')
                    ->select('procurementbody.req_recid AS pr_id', 'procurementbody.id AS col_id', 'procurementbody.description', 'procurementbody.budget_code', 'procurementbody.alternativebudget_code', 'procurementbody.total', 'procurementbody.qty', 'procurementbody.unit_price', 'procurementbody.unit', 'procurementbody.br_dep_code', 'procurementbody.within_budget_code', 'budgetdetail.payment', 'budgetdetail.total AS total_bu')
                    ->get();

                $budget_code = Budgetcode::all();
                $dep_code = Branchcode::all();
                return view('form.paymentpr', compact('budget_code', 'dep_code', 'pr_body', 'pr_id'));
            } else {
                Session::flash('error', 'Request was not approved');
                return redirect()->route('form/payment/new');
            }
        } else {
            Session::flash('error', 'Request was not found');
            return redirect()->route('form/payment/new');
        }
    }

    public function fromProcurementNew(Request $request)
    {
        try {
            $pr_id_from = $request->pr_id;

            $pr_id = explode(',', $pr_id_from);

            $pr_body = Procurementbody::join('budgetdetail', 'budgetdetail.budget_code', 'procurementbody.budget_code')
                ->whereIn('procurementbody.req_recid', $pr_id)
                ->where('procurementbody.paid', 'N')
                ->select('procurementbody.req_recid AS pr_id', 'procurementbody.id AS col_id', 'procurementbody.description', 'procurementbody.budget_code', 'procurementbody.alternativebudget_code', 'procurementbody.total', 'procurementbody.total_khr', 'procurementbody.qty', 'procurementbody.unit_price', 'procurementbody.unit_price_khr', 'procurementbody.unit', 'procurementbody.br_dep_code', 'procurementbody.within_budget_code', 'budgetdetail.payment', 'budgetdetail.total AS total_bu')
                ->get();
            if (count($pr_body) >= 1) {
                $cur_ccy = Requester::whereIn('req_recid', $pr_id)->select('ccy')->groupby('ccy')->get();
                if (count($cur_ccy) > 1) {
                    Session::flash('error', 'Different currency');
                    return redirect()->route('form/payment/new');
                }

                $procurement_bottom = Procurementbottom::where('req_recid', $pr_id[0])->first();
                $general = $procurement_bottom->general;
                $loan_general = $procurement_bottom->loan_general;
                $mortage = $procurement_bottom->mortage;
                $busines = $procurement_bottom->busines;
                $personal = $procurement_bottom->personal;
                $card_general = $procurement_bottom->card_general;
                $debit_card = $procurement_bottom->debit_card;
                $credit_card = $procurement_bottom->credit_card;
                $trade_general = $procurement_bottom->trade_general;
                $bank_guarantee = $procurement_bottom->bank_guarantee;
                $letter_of_credit = $procurement_bottom->letter_of_credit;
                $deposit_general = $procurement_bottom->deposit_general;
                $casa_individual = $procurement_bottom->casa_individual;
                $td_individual = $procurement_bottom->td_individual;
                $casa_corporate = $procurement_bottom->casa_corporate;
                $td_corporate = $procurement_bottom->td_corporate;
                $sagement_general = $procurement_bottom->sagement_general;
                $sagement_bfs = $procurement_bottom->sagement_bfs;
                $sagement_rfs = $procurement_bottom->sagement_rfs;
                $sagement_pb = $procurement_bottom->sagement_pb;
                // $pr_id=['pr_0001','pr_0002'];
                $array_compare = [];
                foreach ($pr_id as $key => $value) {
                    $procurement_bottom_add = DB::table('procurementbottom')->where('req_recid', $value)
                        ->where([
                            'general' => $general,
                            'loan_general' => $loan_general,
                            'mortage' => $mortage,
                            'busines' => $busines,
                            'personal' => $personal,
                            'card_general' => $card_general,
                            'debit_card' => $debit_card,
                            'credit_card' => $credit_card,
                            'trade_general' => $trade_general,
                            'bank_guarantee' => $bank_guarantee,
                            'letter_of_credit' => $letter_of_credit,
                            'deposit_general' => $deposit_general,
                            'casa_individual' => $casa_individual,
                            'td_individual' => $td_individual,
                            'casa_corporate' => $casa_corporate,
                            'td_corporate' => $td_corporate,
                            'sagement_general' => $sagement_general,
                            'sagement_bfs' => $sagement_bfs,
                            'sagement_rfs' => $sagement_rfs,
                            'sagement_pb' => $sagement_pb
                        ])
                        ->first();
                }

                $budget_code = Budgetcode::all();
                $dep_code = Branchcode::all();
                return view('form.paymentpr', compact('budget_code', 'dep_code', 'pr_body', 'pr_id_from', 'cur_ccy', 'procurement_bottom'));
            } else {
                Session::flash('error', 'Request was not found');
                return redirect()->route('form/payment/new');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function index()
    {
        $budget_code = Budgetcode::orderByRaw("CASE
                                          WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                          ELSE 1
                                      END")
                          ->orderBy('budget_code', 'desc')
                          ->get();
        $alternative_budget_codes = Budgetcode::whereNotIn('budget_code', ['NA', 'NO'])->orderBy('budget_code', 'desc')->get();

        $dep_code = Branchcode::orderBy('branch_code', 'desc')->get();

        $pr_approve_notpaid = DB::table('tasklist')
            ->join('procurementbody', 'procurementbody.req_recid', 'tasklist.req_recid')
            ->where(['tasklist.req_type' => '1', 'tasklist.req_status' => '005', 'procurementbody.paid' => 'N'])
            ->select('tasklist.req_recid')
            ->groupby('tasklist.req_recid')
            ->get();

        return view('form.payment', compact('budget_code', 'alternative_budget_codes', 'dep_code', 'pr_approve_notpaid'));
    }

    public function paymentSave(Request $request)
    {
        // allocate product segment less 100
        $allocate_product = $request->general + $request->loan_general + $request->mortgage + $request->business + $request->personal + $request->card_general + $request->debit_card + $request->credit_card +
            $request->trade_general + $request->bank_guarantee + $request->letter_of_credit + $request->deposit_general +
            $request->casa_individual + $request->td_individual + $request->casa_corporate + $request->td_corporate;
        $allocate_segment = $request->general_segment + $request->bfs + $request->rfs + $request->pb + $request->pcp + $request->afs;
        if ($allocate_product !== 100 or $allocate_segment !== 100) {
            Session::flash('error', 'Sum of value Product and Sagment must be 100');
            return redirect()->back();
        }
        // end
        DB::beginTransaction();
        try {
            $req_recid = $request->req_recid;

            if (!empty($req_recid)) {
                $submit = $request->submit;
                if ($submit == 'submit') {
                    $requester = Requester::firstOrNew(['req_recid' => $req_recid]);
                    $requester->subject = $request->subject;
                    $requester->save();
                    $budget_code = Paymentbody::where('req_recid', $req_recid)->first();

                    $assign_back_by = Tasklist::where('req_recid', $req_recid)->where('assign_back_by', null)->first();

                    $within_budget_tasklist = Paymentbody::where(['req_recid' => $req_recid, 'within_budget_code' => 'N'])->first();
                    if (!empty($within_budget_tasklist)) {
                        $tasklist_buget_cond = 'N';
                    } else {
                        $tasklist_buget_cond = 'Y';
                    }
                    if (!empty($assign_back_by)) {
                        //if user saved request at least 6 days
                        if($assign_back_by->req_status == "001"){
                            $now = Carbon::now()->toDateTimeString();  
                            $interval = $assign_back_by->created_at->diffInDays($now); 
                            if($interval > 6){
                                Session::flash('error', "You should submit your request after saved at least 6 days! Please delete this request and create one new.");
                                \DB::commit();
                                return redirect()->back();
                            }
                        }
                        $review_approve = Reviewapprove::firstOrNew(['req_recid' => $req_recid]);
                        $review_approve->req_recid = $req_recid;
                        $review_approve->budget_owner = 'accounting';

                        $approve_email = '';
                        $approve_role  = '';
                        $approve_detail = $request->slc_approve;
                        if (!empty($approve_detail)) {
                            $approve = explode('/', $approve_detail);
                            $approve_email = $approve[0];
                            $approve_role  = $approve[1];
                        }

                        /** if user skip reviewer then it will skp to accounting group */
                        $firstReviewer    = $this->transformApprover($request->slc_review);
                        $secondReviewer   = $this->transformApprover($request->reviewer1);
                        $thirdReviewer    = $this->transformApprover($request->reviewer2);
                        $forthReviewer    = $this->transformApprover($request->reviewer3);
                        if ($request->slc_review) {
                            $checker_detail = $request->slc_review;
                            $checker        = explode('/', $checker_detail);

                            $checker_email = $firstReviewer->email;
                            $checker_role  = $checker[1];
                            $stepNumber    = 1;
                            $review_approve->review = $checker_email;
                            $review_approve->second_review = $secondReviewer->email;
                            $review_approve->third_review = $thirdReviewer->email;
                            $review_approve->fourth_reviewer = $forthReviewer->email;
                        } else {

                            /** check if requester in finance or accounting group , so we auto skip accounting reviewer*/
                            $currentUser = Auth::user();
                            $isFIAGroup = Groupid::where('email', $currentUser->email)->where('group_id', 'GROUP_ACCOUNTING')->where('status',1)->first();
                            if ($isFIAGroup) {
                                $checker_email = $approve_email;
                                $checker_role  = $approve_role;
                                $stepNumber    = 6;
                            } else {
                                $checker_email = 'accounting';
                                $checker_role  = 2;
                                $stepNumber    = 5;
                            }
                        }

                        $review_approve->approve = $approve_email;
                        $review_approve->save();

                        $update_status = [
                            'next_checker_group' => $checker_email,
                            'next_checker_role'  => $checker_role,
                            'within_budget'      => $tasklist_buget_cond,
                            'step_number'        => $stepNumber,
                            'req_status'         => '002'
                        ];
                        $checker_mail = $checker_email;
                        $doer_action = 'Submitted Request';
                    } else {
                        $get_assignaback_info = Tasklist::where('req_recid', $req_recid)->first();
                        $update_status = [
                            'next_checker_group' => $get_assignaback_info->assign_back_by,
                            'next_checker_role'  => $get_assignaback_info->by_role,
                            'step_number'        => $get_assignaback_info->by_step,
                            'within_budget'      => $tasklist_buget_cond,
                            'assign_back_by'     => null,
                            'by_step'            => null,
                            'by_role'            => null,
                            'req_status'         => '002'
                        ];
                        $checker_mail = $get_assignaback_info->assign_back_by;
                        $doer_action = 'Resubmitted Request';
                    }

                    Tasklist::where('req_recid', $req_recid)->update($update_status);

                    $dt = Carbon::now();
                    $date_time = $dt->toDayDateTimeString();
                    $activity_code = 'A001';
                    $comment = $request->comment;
                    $req_email = $request->req_email;
                    $req_name = $request->req_name;
                    $req_branch = $request->req_department;
                    $req_position = $request->req_position;
                    $doer_role = 'Requester';
                    $requester = Defaultsave::auditlogSave($req_recid, $req_email, $req_name, $req_branch, $req_position, '2', $activity_code, $comment, $doer_role, $doer_action);

                    $req_email = Auth::user()->email;
                    $due_date = $request->expDate;
                    $ref = $request->pr_ref;
                    $address = $request->address_who;
                    $contact_no = $request->contact_no;
                    $id_no = $request->id_no;
                    $company = $request->for_who;
                    $tel = $request->tel;
                    $bank_address = $request->bank_address;
                    $bank_name = $request->bank_name;
                    $swift_code = $request->swift_code;
                    $account_number = $request->account_number;
                    $account_name = $request->account_name;
                    $category = $request->category[0];
                    $type = $request->type[0];
                    $remarkable = $request->remarkable;

                    $Payment = Payment::firstOrNew(['req_recid' => $req_recid]);
                    $Payment->req_email = $req_email;

                    $Payment->type = $type;
                    $Payment->category = $category;
                    $Payment->account_name = $account_name;
                    $Payment->account_number = $account_number;
                    $Payment->bank_name = $bank_name;
                    $Payment->swift_code = $swift_code;

                    $Payment->bank_address = $bank_address;
                    $Payment->tel = $tel;
                    $Payment->company = $company;
                    $Payment->id_no = $id_no;
                    $Payment->contact_no = $contact_no;
                    $Payment->address = $address;
                    $Payment->ref = $ref;
                    $Payment->remarkable = $remarkable;

                    $Payment->save();

                    $general = $request->general;
                    $loan_general = $request->loan_general;
                    $mortage = $request->mortgage;
                    $busines = $request->business;
                    $personal = $request->personal;
                    $card_general = $request->card_general;
                    $debit_card = $request->debit_card;
                    $credit_card = $request->credit_card;
                    $trade_general = $request->trade_general;
                    $bank_guarantee = $request->bank_guarantee;
                    $letter_of_credit = $request->letter_of_credit;
                    $deposit_general = $request->deposit_general;
                    $casa_individual = $request->casa_individual;
                    $td_individual = $request->td_individual;
                    $casa_corporate = $request->casa_corporate;
                    $td_corporate = $request->td_corporate;
                    $sagement_general = $request->general_segment;
                    $sagement_bfs = $request->bfs;
                    $sagement_rfs = $request->rfs;
                    $sagement_pb = $request->pb;
                    $sagement_pcp = $request->pcp;
                    $sagement_afs = $request->afs;

                    $update_payment_bottom = [
                        "general" => $general,
                        "loan_general" => $loan_general,
                        "mortage" => $mortage,
                        "busines" => $busines,
                        "personal" => $personal,
                        "card_general" => $card_general,
                        "debit_card" => $debit_card,
                        "credit_card" => $credit_card,
                        "trade_general" => $trade_general,
                        "bank_guarantee" => $bank_guarantee,
                        "letter_of_credit" => $letter_of_credit,
                        "deposit_general" => $deposit_general,
                        "casa_individual" => $casa_individual,
                        "td_individual" => $td_individual,
                        "casa_corporate" => $casa_corporate,
                        "td_corporate" => $td_corporate,
                        "sagement_general" => $sagement_general,
                        "sagement_bfs" => $sagement_bfs,
                        "sagement_rfs" => $sagement_rfs,
                        "sagement_pb" => $sagement_pb,
                        "sagement_pcp" => $sagement_pcp,
                        "sagement_afs" => $sagement_afs
                    ];
                    Paymentbottom::where('req_recid', $req_recid)->update($update_payment_bottom);

                    $due_expect_date = $request->expDate;
                    $ref = $request->refNumber;
                    $subject = $request->subject;
                    $ccy = $request->currency;

                    $requester = Defaultsave::resubmitRequest($req_recid, $req_email, $req_name, $req_branch, $req_position, '2', $due_expect_date, $ref, $subject, $ccy, Auth::user()->email);
                    $pr_col_id = $request->pr_col_id;
                    foreach ($pr_col_id as $key => $value) {
                        Procurementbody::where('id', $value)->update(['paid' => 'Y']);
                    }

                    $paymentbottom = Paymentbottom::firstOrNew(['req_recid' => $req_recid]);
                    $paymentbottom->remarks = $request->remarks_product_segment;
                    $paymentbottom->save();
                    /** trigger budget code remaining amount */
                    /**@var Payment $payment */
                    $payment = Payment::firstWhere('req_recid', $request->req_recid);
                    if ($payment) {
                        /**@var User $user */
                        $user = Auth::user();
                        $user->submitPayment($payment);
                    }
                    // vuthean update discount and deposit
                    $payment_body = Paymentbody::where('req_recid',$request->req_recid)->get();
                    $requester_ccy = Requester::firstOrNew(['req_recid' => $req_recid]);
                    $requester_ccy->update(['due_expect_date'=>$due_expect_date]);
                    if ($requester_ccy->ccy == 'KHR') {
                        $conversion = 4000;
                    } else {
                        $conversion = 1;
                    }
                    $discount_saved = (float)Str::remove(',', $request->discount)/$conversion;
                    $deposit_saved = (float)Str::remove(',', $request->deposit)/$conversion;
                    $vat_change = (float)Str::remove(',', $request->vat_change)/$conversion;
                    $wht_change = (float)Str::remove(',', $request->wht_change)/$conversion;
                    foreach($payment_body as $pbody){
                        $sum = $pbody->total;
                        $data_update_item =[
                            "discount" => $discount_saved,
                            "discount_khr" => (float)$discount_saved*4000,
                            "deposit" => $deposit_saved,
                            "deposit_khr" => (float)$deposit_saved*4000,
                            "vat" => (float)$vat_change,
                            "vat_khr" => (float)$vat_change*4000,
                            "wht" => (float)$wht_change,
                            "wht_khr" => (float)$wht_change*4000,
                            "net_payable" => (float)$sum - (float)$discount_saved + (float)$vat_change - (float)$wht_change - (float)$deposit_saved,
                            "net_payable_khr" => ((float)$sum - (float)$discount_saved + (float)$vat_change - (float)$wht_change - (float)$deposit_saved)*4000
                        ];
                        Paymentbody::where('req_recid', $req_recid)->update($data_update_item);
                    }

                    // New attach

                    $attach_remove = $request->att_remove;
                    if (!empty($attach_remove)) {
                        $att_delete = explode(',', $attach_remove);
                        Documentupload::whereIn('id', $att_delete)->delete();
                        DB::commit();
                    }

                    if ($request->hasFile('fileupload')) {
                        if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                            File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                        }

                        $destinationPath = storage_path() . '/uploads/' . $req_recid . '/';
                        $destinationPath_db = '/uploads/' . $req_recid . '/';
                        foreach ($request->fileupload as $photo) {
                            $file_name = $photo->getClientOriginalName();
                            $photo->move($destinationPath, $file_name);
                            $upload = new Documentupload();
                            $upload->req_recid = $req_recid;
                            $upload->filename = $file_name;
                            $upload->filepath = $destinationPath_db . $file_name;
                            $upload->uuid = Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS);
                            $upload->doer_email = $req_email;
                            $upload->doer_name = $req_name;
                            $upload->activity_form = 1;
                            $upload->activity_datetime = $date_time;
                            $upload->save();
                        }
                    }

                    $send_mail = new Sendemail();
                    $content = 'New Payment Request for your review/approval.';
                    $subject = 'Payment Request';

                    $return_email = $send_mail->sendEmailProcurementRequest($content, $req_recid, $req_name, $req_branch, $req_position, $subject, $checker_mail, $req_email, $comment,$request->subject);
                    if ($return_email == 'fail') {
                        Session::flash('success', 'Request submitted but email did not send');
                    } else {
                        Session::flash('success', $return_email);
                    }
                    DB::commit();
                    return redirect()->back();
                }
            }

            /** create new payment  */
            $currentUser = Auth::user();
            $Payment = new Payment();
            $Payment->req_email      = $currentUser->email;
            $Payment->req_date       = date('d/m/y');
            $Payment->due_date       = $request->expDate;
            $Payment->ref            = $request->pr_ref;
            $Payment->address        = $request->address_who;
            $Payment->contact_no     = $request->contact_no;
            $Payment->id_no          = $request->id_no;
            $Payment->company        = $request->for_who;
            $Payment->tel            = $request->tel;
            $Payment->bank_address   = $request->bank_address;
            $Payment->bank_name      = $request->bank_name;
            $Payment->swift_code      = $request->swift_code;
            $Payment->account_number = $request->account_number;
            $Payment->account_name   = $request->account_name;
            $Payment->category       = $request->category[0];
            $Payment->type           = $request->type[0];
            $Payment->remarkable     = $request->remarkable;
            $Payment->save();

            $date_time      = Carbon::now()->toDayDateTimeString();
            $ref            = $request->pr_ref;
            $address        = $request->address_who;
            $contact_no     = $request->contact_no;
            $id_no          = $request->id_no;
            $company        = $request->for_who;
            $tel            = $request->tel;
            $bank_address   = $request->bank_address;
            $bank_name      = $request->bank_name;
            $swift_code      = $request->swift_code;
            $account_number = $request->account_number;
            $account_name   = $request->account_name;
            $category       = $request->category[0];
            $type           = $request->type[0];
            $pr_col_id      = $request->pr_col_id;
            $last_id        = $Payment->id;

            /** find one payment */
            $payment = Payment::where('id', $last_id)->select('req_recid')->first();

            //Requester table
            $req_recid       = $payment->req_recid;
            $req_email       = $currentUser->email;
            $req_name        = "{$currentUser->firstname} {$currentUser->lastname}";
            $req_branch      = $currentUser->department;
            $req_position    = $currentUser->position;
            $due_expect_date = $request->expDate;
            $subject         = $request->subject;
            $ccy             = $request->currency;

            // procurementbody loop
            $description = $request->description;
            $br_dep_code = $request->br_dep_code;
            $budget_code = $request->budget_code;
            $unit        = $request->unit;
            $qty         = $request->qty;
            $inv_no      = $request->invoice;
            $sub_total   = $request->sub_total;
            $vat         = $request->vat;
            $wht         = $request->wht;
            $deposit     = $request->deposit;
            $net_payable = '122';
            $unit_price  = $request->unit_price;
            $vat_items    = $request->vat_item;

            $budget_request = [];
            for ($i = 0; $i < count($budget_code); $i++) {
                array_push($budget_request, $budget_code[$i]);
            }

            $alternativebudget_code = $request->alternative_budget_code;
            if ($alternativebudget_code == '0') {
                for ($i = 0; $i < count($alternativebudget_code); $i++) {
                    array_push($budget_request, $alternativebudget_code[$i]);
                }
            }

            $total_2 = [];
            $sum = 0;

            for ($i = 0; $i < count($unit_price); $i++) {
                if ($ccy == 'KHR') {
                    $conversion = 4000;
                    if(!$vat_items){
                        $vat_item_khr =0;
                    }else{
                        $vat_item_khr = $vat_items[$i] ;
                        $vat_item = $vat_items[$i]/$conversion;
                    }
                } else {
                    $conversion = 1;
                    $vat_item = $vat_items[$i];
                    $vat_item_khr = $vat_items[$i]*$conversion;
                    if(!$vat_item){
                        $vat_item =0;
                    }
                }
                $unitprice = $unit_price[$i] / $conversion;
                $total_1 = ((float)$qty[$i] * (float)$unitprice) + (float)$vat_item;

                $budget = Budgetcode::where('budget_code', $budget_code[$i])->first();
                $alternative_budget = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();
                if (!empty($alternative_budget)) {
                    $alternative_budget_remain = $alternative_budget->temp_payment;
                } else {
                    $alternative_budget_remain = 0;
                }

                if ($budget_code[$i] == $alternativebudget_code[$i]) {
                    $alternative_budget = null;
                    $alternative_budget_remain = 0;
                }

                $budget_remain = $budget->temp_payment;

                $withinbudget_cond = (float)$budget_remain + (float)$alternative_budget_remain - (float)$total_1;

                if ($withinbudget_cond >= 0) {
                    $withinbudget = 'Y';
                    if (empty($alternative_budget)) {
                        $budget_spent = $total_1;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_1) {
                            $budget_spent = $total_1;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;
                            $budget_al_spent = (float)$total_1 - (float)$budget_spent;
                        }
                    }
                } else {
                    $withinbudget = 'N';

                    if (empty($alternative_budget)) {
                        $current_budget_proc = Budgetcode::where('budget_code', $budget_code[$i])->first();
                        $budget_spent = $current_budget_proc->temp_payment;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_1) {
                            $budget_spent = (float)$budget_remain - (float)$total_1;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;

                            $budget_al_spent = $alternative_budget_remain;
                        }
                    }
                }

                array_push($total_2, $total_1);
                $sum += $total_1;
                $total     = $total_1;
                $sub_total = $sum;

                $discount_usd = (float)$request->discount / (float)$conversion;
                $vat = (float)$request->vat / (float)$conversion;
                $wht = (float)$request->wht / (float)$conversion;
                $deposit = (float)$request->deposit / (float)$conversion;
                $unit_price_khr = (float)$unitprice * 4000;
                $total_khr = (float)$qty[$i] * (float)$unit_price_khr + (float)$vat_item_khr;

                $total_khr = (float)$total * 4000;
                $sub_total_khr = (float)$sub_total * 4000;
                $discount_khr = (float)$discount_usd * 4000;
                $vat_khr = $vat * 4000;
                $wht_khr = $wht * 4000;
                $deposit_khr = (float)$deposit * 4000;

                $paymentbody = new Paymentbody();
                $paymentbody->req_recid              = $req_recid;
                $paymentbody->description            = $description[$i];
                $paymentbody->br_dep_code            = $br_dep_code[$i];
                $paymentbody->budget_code            = $budget_code[$i];
                $paymentbody->unit                   = $unit[$i];
                $paymentbody->qty                    = $qty[$i];
                $paymentbody->vat_item               = $vat_item;
                $paymentbody->vat_item_khr           = $vat_item_khr;
                $paymentbody->unit_price             = $unitprice;
                $paymentbody->total                  = $total;
                $paymentbody->budget_use             = $budget_spent;
                $paymentbody->alternative_use        = $budget_al_spent;
                $paymentbody->inv_no                 = $inv_no[$i];
                $paymentbody->old_payment_remaining  = $budget->payment_remaining;
                $paymentbody->alternativebudget_code = $alternativebudget_code[$i];

                if (!empty($pr_col_id)) {
                    $paymentbody->pr_col_id = $pr_col_id[$i];
                }

                $paymentbody->within_budget_code = $withinbudget;

                if (!empty($alternative_budget)) {
                    $total_bud = $alternative_budget->total;
                } else {
                    $total_bud = 0;
                }

                /** check total ytd expens */
                $totalYTDExpense = 0;
                if ($budget) {
                    $totalYTDExpense = (float)$budget->total - (float)$budget->payment_remaining;
                }

                if ($alternative_budget) {
                    $altYTDExpense = (float)$alternative_budget->total - (float)$alternative_budget->payment_remaining;
                    $totalYTDExpense = (float)$totalYTDExpense + (float)$altYTDExpense;
                }

                $paymentbody->ytd_expense = $totalYTDExpense;
                $paymentbody->total_budget = (float)$budget->total + (float)$total_bud;
                $paymentbody->sub_total = $sub_total;
                $paymentbody->discount = $discount_usd;
                $paymentbody->vat = $vat;
                $paymentbody->wht = $wht;
                $paymentbody->deposit = $deposit;
                $net_payable = (float)$sum - (float)$discount_usd + (float)$vat - (float)$wht - (float)$deposit;
                $paymentbody->net_payable = $net_payable;

                $paymentbody->unit_price_khr = $unit_price_khr;
                $paymentbody->total_khr = $total_khr;
                $paymentbody->sub_total_khr = $sub_total_khr;
                $paymentbody->discount_khr = $discount_khr;
                $paymentbody->vat_khr = $vat_khr;
                $paymentbody->wht_khr = $wht_khr;
                $paymentbody->deposit_khr = $deposit_khr;
                $net_payable_khr = (float)$sum * 4000 - (float)$discount_khr + (float)$vat_khr - (float)$wht_khr - (float)$deposit_khr;
                $paymentbody->net_payable_khr = $net_payable_khr;

                $paymentbody->save();

                $remainin = Budgetcode::where('budget_code', $budget_code[$i])->first();
                if ($total_1 > $remainin->temp_payment) {
                    $procurement_budget = ['temp_payment' => '0'];
                } else {
                    $procurement_remaining = (float)$remainin->temp_payment - (float)$total_1;
                    $procurement_budget = ['temp_payment' => $procurement_remaining];
                }

                Budgetcode::where('budget_code', $budget_code[$i])->update($procurement_budget);

                $remainin_al = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();
                // return
                if (!empty($remainin_al)) {
                    if ($total_1 > $remainin_al->temp_payment + $remainin->temp_payment) {
                        $procurement_budget_al = ['temp_payment' => '0'];
                    } else {
                        $procurement_remaining_al = (float)$remainin_al->temp_payment - ((float)$total_1 - (float)$remainin->temp_payment);
                        $procurement_budget_al = ['temp_payment' => $procurement_remaining_al];
                    }
                    Budgetcode::where('budget_code', $alternativebudget_code[$i])->update($procurement_budget_al);
                }

                $budget_hsitory = new Budgethistory();
                $budget_hsitory->req_recid = $req_recid;
                $budget_hsitory->budget_code = $budget_code[$i];
                $budget_hsitory->alternative_budget_code = $alternativebudget_code[$i];
                $budget_hsitory->budget_amount_use = $budget_spent;
                $budget_hsitory->alternative_amount_use = $budget_al_spent;
                $budget_hsitory->save();
            }

            $general = $request->general;
            $loan_general = $request->loan_general;
            $mortage = $request->mortgage;
            $busines = $request->business;
            $personal = $request->personal;
            $card_general = $request->card_general;
            $debit_card = $request->debit_card;
            $credit_card = $request->credit_card;
            $trade_general = $request->trade_general;
            $bank_guarantee = $request->bank_guarantee;
            $letter_of_credit = $request->letter_of_credit;
            $deposit_general = $request->deposit_general;
            $casa_individual = $request->casa_individual;
            $td_individual = $request->td_individual;
            $casa_corporate = $request->casa_corporate;
            $td_corporate = $request->td_corporate;
            $sagement_general = $request->general_segment;
            $sagement_bfs = $request->bfs;
            $sagement_rfs = $request->rfs;
            $sagement_pb = $request->pb;
            $sagement_pcp = $request->pcp;
            $sagement_afs = $request->afs;

            $paymentbottom = new Paymentbottom();

            $paymentbottom->req_recid = $req_recid;
            $paymentbottom->general = $general;
            $paymentbottom->loan_general = $loan_general;
            $paymentbottom->mortage = $mortage;
            $paymentbottom->busines = $busines;
            $paymentbottom->personal = $personal;
            $paymentbottom->card_general = $card_general;
            $paymentbottom->debit_card = $debit_card;
            $paymentbottom->credit_card = $credit_card;
            $paymentbottom->trade_general = $trade_general;
            $paymentbottom->bank_guarantee = $bank_guarantee;
            $paymentbottom->letter_of_credit = $letter_of_credit;
            $paymentbottom->deposit_general = $deposit_general;
            $paymentbottom->casa_individual = $casa_individual;
            $paymentbottom->td_individual = $td_individual;
            $paymentbottom->casa_corporate = $casa_corporate;
            $paymentbottom->td_corporate = $td_corporate;
            $paymentbottom->sagement_general = $sagement_general;
            $paymentbottom->sagement_bfs = $sagement_bfs;
            $paymentbottom->sagement_rfs = $sagement_rfs;
            $paymentbottom->sagement_pb = $sagement_pb;
            $paymentbottom->sagement_pcp = $sagement_pcp;
            $paymentbottom->sagement_afs = $sagement_afs;
            $paymentbottom->remarks = $request->remarks_product_segment;

            $paymentbottom->save();

            if ($request->hasFile('fileupload')) {
                if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                    File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                }

                $destinationPath = storage_path() . '/uploads/' . $req_recid . '/';
                $destinationPath_db = '/uploads/' . $req_recid . '/';
                foreach ($request->fileupload as $photo) {
                    $file_name = $photo->getClientOriginalName();
                    $photo->move($destinationPath, $file_name);
                    $upload = new Documentupload();
                    $upload->req_recid = $req_recid;
                    $upload->filename = $file_name;
                    $upload->filepath = $destinationPath_db . $file_name;
                    $upload->uuid = Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS);
                    $upload->doer_email = $req_email;
                    $upload->doer_name = $req_name;
                    $upload->activity_form = 1;
                    $upload->activity_datetime = $date_time;
                    $upload->save();
                }
            }

            $requester = Defaultsave::defaultSave($req_recid, $req_email, $req_name, $req_branch, $req_position, '2', $due_expect_date, $ref, $subject, $ccy);

            for ($i = 0; $i < count($unit_price); $i++) {
                $budget_code_temp = Budgetcode::where('budget_code', $budget_code[$i])->first();
                $reset_temp = ['temp_payment' => $budget_code_temp->payment_remaining];
                Budgetcode::where('budget_code', $budget_code[$i])->update($reset_temp);
                if ($alternativebudget_code[$i] !== '0') {
                    if ($alternativebudget_code[$i] !== 'N/A') {
                        $budget_code_temp = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();

                        $reset_temp = ['temp_payment' => $budget_code_temp->payment_remaining];
                        Budgetcode::where('budget_code', $alternativebudget_code[$i])->update($reset_temp);
                    }
                }
            }
            DB::commit();

            return Redirect::to('form/payment/detail/' . Crypt::encrypt($req_recid . '___no'));
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function paymentDetail($req_recid)
    {
        try {
            $param_url = Crypt::decrypt($req_recid);
            $after_split = explode('___', $param_url);
            $req_recid = $after_split[0];
            $param_url_response = $after_split[1];
            $review = '0';
            $approve = '0';
            $requester = '0';
            $query = '0';
            $assign_back = '0';
            $budget_code = Budgetcode::orderByRaw("CASE
                                                        WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                        ELSE 1
                                                    END")
                                            ->orderBy('budget_code', 'desc')
                                            ->get();
            $alternative_budget_codes = $budget_code->whereNotIn('budget_code', ['NA', 'NO'])->all();
            $dep_code = Branchcode::orderBy('branch_code', 'desc')->get();

            $requester_cond = Requester::where('req_recid', $req_recid)->first();
            $requester_email = $requester_cond->req_email;

            $task_listing = Tasklist::where('req_recid', $req_recid)->first();

            $requester_condition = Tasklist::where(['req_recid' => $req_recid, 'req_email' => Auth::user()->email, 'next_checker_group' => '1', 'next_checker_role' => '1', 'req_status' => '001'])->first();
            $query_comment = Tasklist::where(['req_recid' => $req_recid, 'req_email' => Auth::user()->email, 'req_status' => '006'])->first();

            if (!empty($requester_condition)) {
                $requester = '1';
            }
            if (!empty($requester_condition) and !empty($task_listing->assign_back_by)) {
                $assign_back = '0';
            }
            if (!empty($query_comment)) {
                $query = '1';
            }
            $review_cond = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => Auth::user()->email])->first();
            if (!empty($review_cond)) {
                $review = '1';
            }
            $condition_view = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('req_email', Auth::user()->email)->whereNotNull('assign_back_by')->first();

            $approve_cond = Tasklist::where('req_recid', $req_recid)->whereIn('next_checker_group', [Auth::user()->email, Auth::user()->group_id])->first();
            if (!empty($approve_cond)) {
                $approve = '1';
            }

            $total_all = Paymentbody::where('req_recid', $req_recid)->get();
             /**get budget detail */
             $budgetCodes = collect($total_all)->pluck('budget_code');
             $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                     ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                     ->get();
             $budgetcode_na = $this->getBudgetNA($budgetCodes);
             /**End */

            $total_spent_all = [];
            $total_spent = 0;
            foreach ($total_all as $key => $value) {
                $total_spent += $value->total;
            }

            $top = [
                'req_recid' => $requester_cond->req_recid,
                'req_name' => $requester_cond->req_name,
                'req_email' => $requester_cond->req_email,
                'req_branch' => $requester_cond->req_branch,
                'req_position' => $requester_cond->req_position,
                'req_date' => $requester_cond->req_date,
                'due_expect_date' => $requester_cond->due_expect_date,
                'ref' => $requester_cond->ref,
                'subject' => $requester_cond->subject,
                'ccy' => $requester_cond->ccy,
            ];


            $payment = Payment::where('req_recid', $req_recid)->first();

            $top_mid = [
                'type' => $payment->type,
                'category' => $payment->category,
                'account_name' => $payment->account_name,
                'account_number' => $payment->account_number,
                'bank_name' => $payment->bank_name,
                'swift_code' => $payment->swift_code,
                'bank_address' => $payment->bank_address,
                'tel' => $payment->tel,
                'company' => $payment->company,
                'id_no' => $payment->id_no,
                'contact_no' => $payment->contact_no,
                'address' => $payment->address,
                'ref' => $payment->ref,
                'req_date' => $payment->req_date,
                'remarkable' => $payment->remarkable
            ];

            $body = Paymentbody::where('req_recid', $req_recid)->get();

            $body_bottom = Paymentbody::where('req_recid', $req_recid)->orderBy('id', 'desc')->first();

            $budget_his = Budgethistory::where('req_recid', $req_recid)->orderBy('id', 'asc')->first();

            $bottom = Paymentbottom::where('req_recid', $req_recid)->first();

            $document = Documentupload::where('req_recid', $req_recid)->select('id', 'filename', 'filepath', 'doer_email', 'doer_name', 'activity_datetime', 'uuid')->get();

            $description_response = ['resubmit' => $requester, 'reviewer' => $review, 'approver' => $approve];

            $request_status = DB::table('tasklist')
                ->join('recordstatus', 'tasklist.req_status', 'recordstatus.record_status_id')
                ->where('tasklist.req_recid', $req_recid)
                ->select('recordstatus.record_status_description AS status')
                ->first();

            $auditlog = DB::table('auditlog')
                ->join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','auditlog.doer_role','auditlog.doer_action')
                ->get();

            $checker = Groupid::where('email', $requester_cond->req_email)->where('role_id', '!=', 4)->first();
            if (empty($checker)) {
                Session::flash('error', 'No reviewer or approver found');
                return redirect()->back();
            }

            $requester_progress = Requester::where('req_recid', $req_recid)->first();
            $final_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.final')->where('reviewapprove.req_recid', $req_recid)->first();
            $review_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.review')->where('reviewapprove.req_recid', $req_recid)->first();

            $review1_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.second_review')->where('reviewapprove.req_recid', $req_recid)->first();

            $budgetowner_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.budget_owner')->where('reviewapprove.req_recid', $req_recid)->first();
            $approve_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.approve')->where('reviewapprove.req_recid', $req_recid)->first();
            $review_approve = Reviewapprove::where([['req_recid', $req_recid],['approve','']])->first();
            if($review_approve){
                $approve_progress = Groupid::join('users','users.email','groupid.email')->where('groupid.group_id', 'GROUP_CEO')->where('groupid.status',1)->first();
            }
            $insuficient = Tasklist::where('tasklist.req_recid', $req_recid)->where('insufficient', 'Y')->first();
            $pending_at = Tasklist::where('req_recid', $req_recid)->first();
            $pending_at_team = '';
            if ($pending_at->next_checker_group == 'accounting') {
                $pending_at_team = 'Accounting & Finance';
            }
            $pending_at = Tasklist::join('users', 'users.email', 'tasklist.next_checker_group')->where('tasklist.req_recid', $req_recid)->first();

            $group_final = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_ACCOUNTING')->where('groupid.status',1)->get();

            /** find request id */
            $procurement_ref = Requester::where('req_recid', $req_recid)->first();
            $contains = Str::contains($procurement_ref->ref , [',']);
            $rp_ref_no_pr=array();
            // multi request
            if($contains == true){
                $merge_req = explode(',',$procurement_ref->ref,10);
                foreach($merge_req as $req){
                    $cryp_advance = Crypt::encrypt($req . '___no');
                    $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                    $rp_ref_no = [
                            'href' => $url_advance,
                            'value' => $req
                    ];
                    $rp_ref_no_pr[] = $rp_ref_no;
                }
            }else{
                $string_req = $procurement_ref->ref;
                $cryp_advance = Crypt::encrypt($string_req . '___no');
                $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                $rp_ref_no_pr[] = [
                    'href' => $url_advance,
                    'value' => $string_req
                ];
            }
            if ($requester == '1' or !empty($condition_view)) {
                 // multi reviewer
                $group_multiReviewer = Groupid::join('usermgt', 'groupid.email', 'usermgt.email')
                                                ->where('groupid.email', '!=', Auth::user()->email) 
                                                ->whereIn('groupid.role_id', ['2','3'])
                                                ->groupBy('groupid.email')
                                                ->get();

                 $ceo = Groupid::join('usermgt', 'groupid.email', 'usermgt.email')
                                                ->where('groupid.group_id', 'GROUP_CEO') 
                                                ->first();
                $dceo_office = GroupId::select('groupid.email','groupid.role_id','usermgt.fullname')
                        ->join('usermgt', 'groupid.email', 'usermgt.email')
                        ->where('groupid.group_id','GROUP_DCEO_OFFICE')->where('groupid.status',1)->first();
                $dceo_email_office = $dceo_office->email.'/'.$dceo_office->role;
                $dceos = GroupId::select('groupid.email','groupid.role_id','usermgt.fullname')
                        ->join('usermgt', 'groupid.email', 'usermgt.email')
                        ->where('groupid.group_id','GROUP_DCEO')->where('groupid.status',1)->first();

                $group_requester = DB::table('groupid')
                    ->join('usermgt', 'groupid.email', 'usermgt.email')
                    ->where('groupid.group_id', $checker->group_id)
                    ->where('groupid.email', '!=', Auth::user()->email)
                    ->where('groupid.role_id', '!=', '1')
                    ->where('groupid.status','1')
                    ->get();
                if (empty($group_requester)) {
                    Session::flash('error', 'No reviewer or approver found');
                    return redirect()->back();
                }

                $within_ornot = Paymentbody::where(['req_recid' => $req_recid, 'within_budget_code' => 'N'])->first();

                $total_all = Paymentbody::where('req_recid', $req_recid)->get();

                $total_spent_all = [];
                $total_spent = 0;
                foreach ($total_all as $key => $value) {
                    $total_spent += $value->total;
                }

                if ($total_spent <= 10000) {
                    $max_spent = '<=10000';
                } elseif ($total_spent > 10000 and $total_spent <= 50000) {
                    $max_spent = '<=50000';
                } else {
                    $max_spent = '>50000';
                }

                if (!empty($within_ornot)) {
                    if ($max_spent == '<=10000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')->where('groupid.status',1)->get();
                    } elseif ($max_spent == '<=50000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_MEMBER_EXCO')->where('groupid.status',1)->get();
                    } else {
                        $group_approver = 'CFO_ONLY';
                    }
                } else {
                    if ($max_spent == '<=10000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')->where('groupid.status',1)->get();
                    } elseif ($max_spent == '<=50000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_MEMBER_EXCO')->where('groupid.status',1)->get();
                    } else {
                        $group_approver = 'MDOFFICE_ONLY';
                    }
                }

                $pr_id = $req_recid;

                // End First Submit Select reviewer
                return view('approver.payment', compact(
                                                        'procurement_ref',
                                                        'budget_his', 
                                                        'pr_id', 
                                                        'top', 
                                                        'top_mid', 
                                                        'body', 
                                                        'bottom', 
                                                        'document', 
                                                        'description_response', 
                                                        'auditlog', 
                                                        'group_requester', 
                                                        'requester', 
                                                        'condition_view', 
                                                        'review', 'approve', 
                                                        'request_status', 
                                                        'budget_code', 
                                                        'alternative_budget_codes', 
                                                        'dep_code', 
                                                        'group_approver', 
                                                        'group_final', 
                                                        'query', 
                                                        'total_spent', 
                                                        'body_bottom', 
                                                        'pending_at_team',
                                                        'rp_ref_no_pr',
                                                        'totalAndYTD',
                                                        'budgetcode_na',
                                                        'group_multiReviewer',
                                                        'dceo_email_office',
                                                        'dceos',
                                                        'ceo'
                                                    ));
            } else {
                $pr_id = $req_recid;
                $tasklist = Tasklist::where('req_recid', $req_recid)->first();
                $owner = $tasklist->req_email;
                $within_budget = $tasklist->within_budget;
                $step_number = $tasklist->step_number;

                $final_res = 'N';
                $final = Reviewapprove::where('final', Auth::user()->email)->first();
                $final_checker = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('step_number', '>=', 6)->first();

                $last_step = $tasklist->step_number;
                if (!empty($final) and !empty($final_checker)) {
                    $final_res = 'Y';
                }
                /** find approvers */
                $approvalUsers = $payment->getUserApprovalLevel();
                return view('approver.paymentapprove', compact(
                                                            'procurement_ref',
                                                            'insuficient', 
                                                            'pr_id', 
                                                            'request_status', 
                                                            'top', 
                                                            'top_mid', 
                                                            'body', 
                                                            'bottom', 
                                                            'document', 
                                                            'description_response', 
                                                            'auditlog', 
                                                            'requester', 
                                                            'review', 
                                                            'approve', 
                                                            'final_res', 
                                                            'query', 
                                                            'total_spent', 
                                                            'body_bottom', 
                                                            'review_progress', 
                                                            'review1_progress', 
                                                            'budgetowner_progress', 
                                                            'approve_progress', 
                                                            'pending_at', 
                                                            'requester_progress', 
                                                            'final_progress', 
                                                            'pending_at_team', 
                                                            'alternative_budget_codes',
                                                            'rp_ref_no_pr',
                                                            'totalAndYTD',
                                                            'budgetcode_na',
                                                            'approvalUsers',
                                                            'tasklist'
                                                        ));
            }
        } catch (\Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function actionRequest(Request $request)
    {
        DB::beginTransaction();
        try {
            $submit = $request->submit;
            $req_recid = $request->req_recid;

            $condition_approve = Tasklist::where('req_recid', $req_recid)->first();
            $within_budget_param = $condition_approve->within_budget;
            $step_number_param = $condition_approve->step_number;

            $total_all = Paymentbody::where('req_recid', $req_recid)->get();
            $total_spent = 0;
            foreach ($total_all as $key => $value) {
                $total_spent += $value->total;
            }

            if ($total_spent <= 50000) {
                $max_spent = '<=50000';
            } else {
                $max_spent = '>50000';
            }
            $tasklist = Tasklist::where('req_recid', $req_recid)->first();
            $within_budget = $tasklist->within_budget;
            $step_number = $tasklist->step_number;
            $reviewer = Reviewapprove::where(['req_recid' => $req_recid])->first();
            $query_from_user = User::where(['email' => $reviewer->review, 'group_id' => 'accounting'])->first();
            /**if request from accounting team */
            if(!empty($query_from_user) and  $step_number_param < 5){
                if(!$reviewer->second_review and $step_number_param == 1){
                    $step_number_param = 4;
                }elseif(!$reviewer->third_review and $step_number_param == 2){
                    $step_number_param = 4;
                }elseif(!$reviewer->fourth_reviewer and $step_number_param == 3){
                    $step_number_param = 4;
                }else{
                    $step_number_param = $step_number_param;
                }
            }
            if ($within_budget_param == 'N') {
                $next_step = Flowconfig::where(['req_name' => 2, 'within_budget' => $within_budget_param, 'step_number' => $step_number_param, 'amount_request' => $max_spent])->first();
            } else {
                $next_step = Flowconfig::where(['req_name' => 2, 'within_budget' => $within_budget_param, 'step_number' => $step_number_param, 'amount_request' => $max_spent])->first();
            }
            //** if user reviwer is accounting team skip accounting review */
            if (!empty($query_from_user) and $next_step->checker == 'accounting' and $step_number_param < 5) {
                $step_number_param += 1;
                $step_number = $step_number_param;
                if ($within_budget_param == 'N') {
                    $next_step = Flowconfig::where(['req_name' => 2, 'within_budget' => $within_budget_param, 'step_number' => $step_number_param, 'amount_request' => $max_spent])->first();
                } else {
                    $next_step = Flowconfig::where(['req_name' => 2, 'within_budget' => $within_budget_param, 'step_number' => $step_number_param, 'amount_request' => $max_spent])->first();
                }
            }
            switch ($next_step->checker) {
                case 'second_reviewer':
                    $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)->first();
                    $secondReviewer = $checker_email_cond->second_review;
                    if($secondReviewer){
                        $checker_email = $secondReviewer;
                        $checkeR_group = '';
                    }else{
                        $step_number = 4;
                        $checker_email = 'accounting';
                        $checkeR_group = 'GROUP_ACCOUNTING';
                    }
                    break;
                case 'third_reviewer':
                    $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)->first();
                    $thirdReviewer = $checker_email_cond->third_review;
                    if($thirdReviewer){
                        $checker_email = $thirdReviewer;
                        $checkeR_group = '';
                    }else{
                        $step_number = 4;
                        $checker_email = 'accounting';
                        $checkeR_group = 'GROUP_ACCOUNTING';
                    }
                    break;
                case 'forth_reviewer':
                        $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)->first();
                        $fouthReviewer =$checker_email_cond->fourth_reviewer;
                        if($fouthReviewer){
                            $checker_email = $fouthReviewer;
                            $checkeR_group = '';
                        }else{
                            $step_number = 4;
                            $checker_email = 'accounting';
                            $checkeR_group = 'GROUP_ACCOUNTING';
                        }
                        break;
                case 'accounting':
                    $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)->first();
                    $checker_email = $checker_email_cond->budget_owner;
                    $checkeR_group = 'GROUP_ACCOUNTING';
                    break;
                case 'approver':
                    $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)->first();
                    $checker_email = $checker_email_cond->approve;
                    $checkeR_group = '';
                    break;
                case 'MD Office':
                    $checker_email_cond = Groupid::where('group_id', 'GROUP_MDOFFICE')->where('status',1)
                        ->first();
                    $checker_email = $checker_email_cond->email;
                    $checkeR_group = 'GROUP_MDOFFICE';
                    break;
                case 'FINAL':
                    $checker_email_cond = Reviewapprove::where('req_recid', $req_recid)

                        ->first();
                    $checker_email = $checker_email_cond->final;
                    $checkeR_group = 'GROUP_ACCOUNTING';
                    break;
                case 'CEO':
                    $checker_email_cond = Groupid::where('group_id', 'GROUP_CEO')->where('status',1)
                        ->first();
                    $checker_email = $checker_email_cond->email;
                    $checkeR_group = 'GROUP_CEO';
                    break;
                case 'CFO':
                    $checker_email_cond = Groupid::where([['group_id', 'GROUP_CFO'],['is_cfo', '1']])->where('status',1)
                        ->first();
                    $checker_email = $checker_email_cond->email;
                    $checkeR_group = 'GROUP_CFO';
                    break;
                case 'Close':
                    $checker_email = null;
                    $checkeR_group = '';
                    break;
            }

            if ($within_budget == 'N') {
                $max_step = Flowconfig::where(['req_name' => '2', 'within_budget' => $within_budget, 'amount_request' => $max_spent])->selectRaw('MAX(CAST(step_number AS UNSIGNED)) as max_step') ->value('max_step');
            } else {
                $max_step = Flowconfig::where(['req_name' => '2', 'within_budget' => $within_budget, 'amount_request' => $max_spent])->selectRaw('MAX(CAST(step_number AS UNSIGNED)) as max_step') ->value('max_step');
            }
            $tasklist_requester_info = Tasklist::where('req_recid', $req_recid)->first();
            //** find current role */
            $currentStepNumber = $next_step->step_number;
            if(!empty($query_from_user)){
                $currentStepNumber = $next_step->step_number - 1;
            }
            if(empty($query_from_user) and $currentStepNumber >= 5){
                $currentStepNumber = $next_step->step_number - 1;
            }
            if($currentStepNumber < 4){
                $doerRole = 'Reviewer';
            }else{
                $checkCurrentRole = Flowconfig::where([
                                                'req_name' => 2,
                                                'within_budget' => $next_step->within_budget,
                                                'step_number' => $currentStepNumber,
                                                'amount_request' => $next_step->amount_request
                                            ])->first();
                $doerRole = $checkCurrentRole->step_description;

            }
            if ($submit == 'approve') {
                if ($step_number <= $max_step - 2) {
                    $tasklist = [
                        'next_checker_group' => $checker_email,
                        'step_number'        => $step_number + 1,
                    ];
                $content       = 'New Payment Request for your review/approval.';
                } elseif ($step_number <= $max_step - 1) {
                    $tasklist = [
                        'next_checker_group' => $checker_email,
                        'step_number'        => $step_number + 1,
                    ];
                $content       = 'New Payment Request for your review/approval.';
                } else {
                    $tasklist = [
                        'next_checker_group' => '1',
                        'next_checker_role'  => '1',
                        'step_number'        => $step_number,
                        'req_status'         => '005',
                    ];
                    $checker_email = $tasklist_requester_info->req_email;
                    $content = 'Payment Request has been approved successfully.';
                }
                Tasklist::where('req_recid', $req_recid)->update($tasklist);

                $success       = 'Approved';
                $activity_code = 'A002';
                $doerAction = $tasklist_requester_info->checkActionPayment($doerRole);
            } elseif ($submit == 'back') {
                $checker_email_db = Tasklist::where('req_recid', $req_recid)->first();
                $checker_email    = $checker_email_db->req_email;
                $tasklist = [
                    'next_checker_group' => $checker_email,
                    'next_checker_role'  => '1',
                    'step_number'        => '1',
                    'assign_back_by'     => Auth::user()->email,
                    'by_role'            => $checker_email_db->next_checker_role,
                    'by_step'            => $checker_email_db->step_number,
                ];

                Tasklist::where('req_recid', $req_recid)->update($tasklist);

                $content       = 'Payment Request has been assign back/review.';
                $success       = 'Assign Back';
                $activity_code = 'A007';
                $doerAction = 'Assigned Back';

                /** trigger budget code remaining amount */
                $payment = Payment::firstWhere('req_recid', $request->req_recid);
                if ($payment) {
                    /**@var User $user */
                    $user = Auth::user();
                    $user->assignPaymentBack($payment);
                }
            } elseif ($submit == 'query') {
                $activity_code           = 'A008';
                $requester_email_comment = Tasklist::where(['req_email' => Auth::user()->email, 'req_status' => '006'])->first();
                if (!empty($requester_email_comment)) {
                    $tasklist = [
                        'req_status' => '002',
                    ];
                    $checker_email = $tasklist_requester_info->next_checker_group;
                } else {
                    $tasklist = [
                        'req_status' => '006',
                    ];
                    $checker_email = $tasklist_requester_info->req_email;
                }

                Tasklist::where('req_recid', $req_recid)->update($tasklist);
                $success = 'Query';
                $content = 'Payment Request has been query/view comment.';
                $doerAction = 'Queried Back';
            } elseif ($submit == 'backtocfo') {
                $checker_email_cond = Groupid::where([['group_id', 'GROUP_CFO'],['is_cfo', '1']])->where('status',1)
                    ->first();
                $checker_email = $checker_email_cond->email;
                $tasklist = [
                    'next_checker_group' => $checker_email,
                    'next_checker_role'  => '1',
                    'step_number'        => '3',
                    'req_status'         => '002',
                ];

                Tasklist::where('req_recid', $req_recid)->update($tasklist);
                $content       = 'Payment Request has been submitted/approved.';
                $activity_code = 'A003';
                $success       = 'Approved';
                $doerAction = 'Assigned Back';
            } else {
                $tasklist = [
                    'next_checker_group' => 'close',
                    'next_checker_role'  => 'close',
                    'step_number'        => $step_number,
                    'req_status'         => '004',
                ];
                Tasklist::where('req_recid', $req_recid)->update($tasklist);
                $content       = 'Payment Request has been rejected.';
                $activity_code = 'A003';
                $success       = 'Rejected';
                $doerAction = 'Rejected';
                $checker_email = $tasklist_requester_info->req_email;
                $pr_col_id     = Paymentbody::where('req_recid', $req_recid)->where('pr_col_id', '!=', null)->select('pr_col_id')->get();
                if (count($pr_col_id) >= 1) {
                    foreach ($pr_col_id as $key => $value) {
                        Procurementbody::where('id', $value->pr_col_id)->update(['paid' => 'N']);
                    }
                }

                /** trigger budget code remaining amount */
                $payment = Payment::firstWhere('req_recid', $request->req_recid);
                if ($payment) {
                    /**@var User $user */
                    $user = Auth::user();
                    $user->rejectPayment($payment);
                }
            }

            $comment = $request->comment;

            /**curret user */
            $user         = Auth::user();
            $req_email    = $user->email;
            $req_name     = "{$user->firstname} {$user->lastname}";
            $req_branch   = $user->department;
            $req_position = $user->position; 
            Defaultsave::auditlogSave($req_recid, $req_email, $req_name, $req_branch, $req_position, '2', $activity_code, $comment, $doerRole, $doerAction);
            DB::commit();

            $find_mail_group = Emailgroup::where('group_id', $checkeR_group)->first();

            if (!empty($find_mail_group)) {
                $checker_email = $find_mail_group->group_email;
            }
            $request_subject = Requester::where('req_recid', $request->req_recid)->first();
            $req_name     = $tasklist_requester_info->req_name;
            $req_branch   = $tasklist_requester_info->req_branch;
            $req_position = $tasklist_requester_info->req_position;
            $req_email    = $tasklist_requester_info->req_email;

            $send_mail    = new Sendemail();
            $subject      = 'Payment Request';
            $return_email = $send_mail->sendEmailProcurementRequest($content, $req_recid, $req_name, $req_branch, $req_position, $subject, $checker_email, $req_email, $comment, $request_subject->subject);
            if ($return_email == 'fail') {
                Session::flash('success', 'Success but no email send');
            } else {
                Session::flash('success', $success);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function editRow(Request $request)
    {
        DB::beginTransaction();
        try {
            $condition = $request->submit;
            $id = $request->req_recid_edit;
            $req_recid = $request->req_recid_update;
            $description = $request->description_new;
            $inv_no = $request->invoice_new;
            $br_dep_code = $request->branchcode;
            $budget_code = $request->budget_code;
            $alternativebudget_code = $request->alternativebudget_code;
            $unit = $request->unit;
            $qty = $request->qty;
            $vat_item = $request->vat_item;
            $unit_price = $request->unit_price;
            $total_estimate = $request->total_estimate;
            $delivery_date = $request->delivery_date;
            $total = $request->total;
            $within_budget_code = $request->within_budget_code;
            $discount = $request->discount;
            $vat = $request->vat;
            $wht = $request->wht;
            $deposit = $request->deposit;

            $ccy_cond = Requester::where('req_recid', $req_recid)->first();
            $ccy = $ccy_cond->ccy;

            if ($ccy == 'KHR') {
                $conversion = 4000;
            } else {
                $conversion = 1;
            }

            $total_2 = [];
            $sum = [];
            $unitprice = (float)$unit_price / (float)$conversion;
            $vat_items = (float)$vat_item / (float)$conversion;
            $total_1 = (float)$qty * (float)$unitprice + (float)$vat_items;

            if ($conversion == '4000') {
                $total_khr = (float)$qty * (float)$unit_price + (float)$vat_items;
                $unit_price_khr = $unit_price;
                $discount_khr = $discount;
                $vat_khr = $vat;
                $wht_khr = $wht;
                $deposit_khr = $deposit;
                $item_vat_khr =  $vat_item;
            } else {
                $vat_item_khr = $vat_items * 4000;
                $unit_price_khr = $unit_price * 4000;
                $total_khr = ((float)$qty * (float)$unit_price * 4000) + (float)$vat_item_khr;
                $discount_khr = $discount * 4000;
                $vat_khr = $vat * 4000;
                $wht_khr = $wht * 4000;
                $deposit_khr = $deposit * 4000;
                $item_vat_khr =  $vat_items * 4000;
            }

            $budget = Budgetcode::where('budget_code', $budget_code)->first();
            $alternative_budget = Budgetcode::where('budget_code', $alternativebudget_code)->first();
            $payment_item = Paymentbody::where('id',$id)->first();
            if($id == null){
                $payment_remaining = $budget->payment_remaining;
            }else{
                if($budget_code == $payment_item->budget_code){
                    $payment_remaining = $payment_item->old_payment_remaining;
                }else{
                    $payment_remaining = $budget->payment_remaining;
                }
            }
            if (!empty($alternative_budget)) {
                $alternative_budget_remain = $alternative_budget->temp_payment;
            } else {
                $alternative_budget_remain = 0;
            }
            if ($budget_code == $alternativebudget_code) {
                $alternative_budget = null;
                $alternative_budget_remain = 0;
            }
            $budget_remain = $budget->temp_payment;
            $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_1;
            if ($withinbudget_cond >= 0) {
                $withinbudget = 'Y';
                if (empty($alternativebudget_code)) {
                    $budget_spent = $total_1;
                    $budget_al_spent = 0;
                } else {
                    if ($budget_remain > $total_1) {
                        $budget_spent = $total_1;
                        $budget_al_spent = 0;
                    } else {
                        $budget_spent = $budget_remain;
                        $budget_al_spent = $total_1 - $budget_spent;
                    }
                }
            } else {
                $withinbudget = 'N';
                if (empty($alternativebudget_code)) {
                    $current_budget_proc = Budgetcode::where('budget_code', $budget_code)->first();
                    $budget_spent = $current_budget_proc->temp_payment;
                    $budget_al_spent = 0;
                } else {
                    if ($budget_remain > $total_1) {
                        $budget_spent = $budget_remain - $total_1;
                        $budget_al_spent = 0;
                    } else {
                        $budget_spent = $budget_remain;

                        $budget_al_spent = $alternative_budget_remain;
                    }
                }
            }

            array_push($total_2, $total_1);
            $sum = $total_1;

            $total = $total_1;

            $sub_total = $sum;
            $discount_usd = $discount / $conversion;

            $vat = floatval(str_replace(',', '', $vat)) / $conversion;
            $wht = floatval(str_replace(',', '', $wht)) / $conversion;
            $deposit = floatval(str_replace(',', '', $deposit)) / $conversion;

            $unit_price_khr = $unitprice * 4000;
            $total_khr = $qty * $unit_price_khr;

            $total_khr = $total * 4000;
            $sub_total_khr = $sub_total * 4000;
            $discount_khr = $discount_usd * 4000;
            $vat_khr = $vat * 4000;
            $wht_khr = $wht * 4000;
            $deposit_khr = $deposit * 4000;

            if ($budget_code == $alternativebudget_code) {
                $budget_al_spent = 0;
                $alternativebudget_code = 0;
            }
            $budget_his_id = $request->budget_his_id;

            $within_budget_condition = $request->within_budget_code;
            if ($condition == 'update') {
                $result = Paymentbody::firstOrNew(['id' => $id]);
                $result->vat_item = $vat_items;
                $result->vat_item_khr = $item_vat_khr;
                $result->req_recid = $req_recid;
                $result->inv_no = $inv_no;
                $result->description = $description;
                $result->br_dep_code = $br_dep_code;
                $result->budget_code = $budget_code;
                $result->alternativebudget_code = $alternativebudget_code;
                $result->unit = $unit;
                $result->qty = $qty;
                $result->unit_price = $unitprice;
                $result->total = $total;
                $result->old_payment_remaining = $payment_remaining;

                $result->budget_use = $budget_spent;
                $result->alternative_use = $budget_al_spent;

                $result->within_budget_code = $withinbudget;
                if (!empty($alternative_budget)) {
                    $payment_alt = $alternative_budget->payment;
                    $total_bud = $alternative_budget->total;
                } else {
                    $payment_alt = 0;
                    $total_bud = 0;
                }

                $totalYTDExpense = 0;
                if ($budget) {
                    $totalYTDExpense = (float)$budget->total - (float)$budget->payment_remaining;
                }

                if ($alternative_budget) {
                    $altYTDExpense = (float)$alternative_budget->total - (float)$alternative_budget->payment_remaining;
                    $totalYTDExpense = $totalYTDExpense + $altYTDExpense;
                }

                $result->ytd_expense = $totalYTDExpense;
                $result->total_budget = $budget->total + $total_bud;
                $result->sub_total = $sub_total;
                $result->discount = $discount_usd;

                /*** update usd currency */
                $result->vat = $vat;
                $result->wht = $wht;
                $result->deposit = $deposit;
                $net_payable = $sum - $discount_usd + $vat - $wht - $deposit;
                $result->net_payable = $net_payable;

                /** update khr currency */
                $result->vat_khr = $vat_khr;
                $result->wht_khr = $wht_khr;
                $result->deposit_khr = $deposit_khr;
                $net_payable_khr = $sum * 4000 - $discount_khr + $vat_khr - $wht_khr - $deposit_khr;

                $result->unit_price_khr = $unit_price_khr;
                $result->total_khr = $total_khr;
                $result->sub_total_khr = $sub_total_khr;
                $result->discount_khr = $discount_khr;
                $result->net_payable_khr = $net_payable_khr;

                $result->save();

                $budget_hsitory = Budgethistory::firstOrNew(['id' => $budget_his_id]);

                $budget_hsitory->req_recid = $req_recid;
                $budget_hsitory->budget_code = $budget_code;
                $budget_hsitory->alternative_budget_code = $alternativebudget_code;
                $budget_hsitory->budget_amount_use = $budget_spent;
                $budget_hsitory->alternative_amount_use = $budget_al_spent;
                $budget_hsitory->save();

                /*** update order vat, wht and deposit */
                $paymentBodies = Paymentbody::where('req_recid', $req_recid)->where('id', '<>', $id)->get();

                foreach ($paymentBodies as $paymentBody) {
                    $total = bcmul($paymentBody->qty, $paymentBody->unit_price, 3);
                    $net_payable = (((($total - $discount_usd) + $vat) - $wht) - $deposit);
                    $net_payable_khr = ((((($total * 4000) - $discount_khr) + $vat_khr) - $wht_khr) - $deposit_khr);

                    $paymentBody->update([
                        /*** update usd currency */
                        'vat' => $vat,
                        'wht' => $wht,
                        'deposit' => $deposit,
                        'net_payable' => $net_payable,

                        /** update khr currency */
                        'vat_khr' => $vat_khr,
                        'wht_khr' => $wht_khr,
                        'deposit_khr' => $deposit_khr,
                        'net_payable_khr' => $net_payable_khr

                    ]);
                }
                /*** when user try to change vat, discount,  */
                DB::commit();
                Session::flash('success', 'Item updated');
                return redirect()->back();
            } else {
                $pr_col_id = $request->pr_id_s;
                Procurementbody::where('id', $pr_col_id)->update(['paid' => 'N']);
                $result1 = Budgethistory::where('id', $budget_his_id)->delete();
                $result = Paymentbody::find($id);
                $result->delete();
                DB::commit();

                Session::flash('success', 'Item updated');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function generatePDF($req_recid)
    {
        try {
            $param_url = Crypt::decrypt($req_recid);
            $after_split = explode('___', $param_url);

            $req_recid = $after_split[0];
            $param_url_response = $after_split[1];
            $review = '0';
            $approve = '0';
            $requester = '0';
            $query = '0';
            $assign_back = '0';
            $budget_code = Budgetcode::all();
            $alternative_budget_codes = $budget_code->whereNotIn('budget_code', ['NA', 'NO'])->all();
            $dep_code = Branchcode::all();

            $requester_cond = Requester::where('req_recid', $req_recid)->first();
            $requester_email = $requester_cond->req_email;

            $task_listing = Tasklist::where('req_recid', $req_recid)->first();

            $requester_condition = Tasklist::where(['req_recid' => $req_recid, 'req_email' => Auth::user()->email, 'next_checker_group' => '1', 'next_checker_role' => '1', 'req_status' => '001'])->first();
            $query_comment = Tasklist::where(['req_recid' => $req_recid, 'req_email' => Auth::user()->email, 'req_status' => '006'])->first();

            if (!empty($requester_condition)) {
                $requester = '1';
            }
            if (!empty($requester_condition) and !empty($task_listing->assign_back_by)) {
                $assign_back = '0';
            }
            if (!empty($query_comment)) {
                $query = '1';
            }
            $review_cond = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => Auth::user()->email])->first();
            if (!empty($review_cond)) {
                $review = '1';
            }
            $condition_view = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('req_email', Auth::user()->email)->whereNotNull('assign_back_by')->first();

            $approve_cond = Tasklist::where('req_recid', $req_recid)->whereIn('next_checker_group', [Auth::user()->email, Auth::user()->group_id])->first();
            if (!empty($approve_cond)) {
                $approve = '1';
            }

            $total_all = Paymentbody::where('req_recid', $req_recid)->get();

            $total_spent_all = [];
            $total_spent = 0;
            foreach ($total_all as $key => $value) {
                $total_spent += $value->total;
            }

            $top = [
                'req_recid' => $requester_cond->req_recid,
                'req_name' => $requester_cond->req_name,
                'req_email' => $requester_cond->req_email,
                'req_branch' => $requester_cond->req_branch,
                'req_position' => $requester_cond->req_position,
                'req_date' => $requester_cond->req_date,
                'due_expect_date' => $requester_cond->due_expect_date,
                'ref' => $requester_cond->ref,
                'subject' => $requester_cond->subject,
                'ccy' => $requester_cond->ccy,
            ];


            $procurement = Payment::where('req_recid', $req_recid)->first();

            $top_mid = [
                'type' => $procurement->type,
                'category' => $procurement->category,
                'account_name' => $procurement->account_name,
                'account_number' => $procurement->account_number,
                'bank_name' => $procurement->bank_name,
                'swift_code' => $procurement->swift_code,
                'bank_address' => $procurement->bank_address,
                'tel' => $procurement->tel,
                'company' => $procurement->company,
                'id_no' => $procurement->id_no,
                'contact_no' => $procurement->contact_no,
                'address' => $procurement->address,
                'ref' => $procurement->ref,
                'req_date' => $procurement->req_date,
                'remarkable' => $procurement->remarkable
            ];

            $body = Paymentbody::where('req_recid', $req_recid)->get();
             /**get budget detail */
             $budgetCodes = collect($body)->pluck('budget_code');
             $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                     ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                     ->get();
             $budgetcode_na = $this->getBudgetNA($budgetCodes);
             /**End */

            $body_bottom = Paymentbody::where('req_recid', $req_recid)->orderBy('id', 'desc')->first();

            $budget_his = Budgethistory::where('req_recid', $req_recid)->orderBy('id', 'asc')->first();

            $bottom = Paymentbottom::where('req_recid', $req_recid)->first();

            $document = Documentupload::where('req_recid', $req_recid)->select('id', 'filename', 'filepath', 'doer_email', 'doer_name', 'activity_datetime', 'uuid', 'created_at')->get();

            $description_response = ['resubmit' => $requester, 'reviewer' => $review, 'approver' => $approve];

            $request_status = DB::table('tasklist')
                ->join('recordstatus', 'tasklist.req_status', 'recordstatus.record_status_id')
                ->where('tasklist.req_recid', $req_recid)
                ->select('recordstatus.record_status_description AS status')
                ->first();

            $auditlog = DB::table('auditlog')
                ->join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.created_at AS datetime')
                ->get();

            $checker = Groupid::where('email', $requester_cond->req_email)->where('role_id', '!=', 4)->where('status',1)->first();
            if (empty($checker)) {
                Session::flash('error', 'No reviewer or approver found');
                return redirect()->back();
            }

            $requester_progress = Requester::where('req_recid', $req_recid)->first();
            $final_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.final')->where('reviewapprove.req_recid', $req_recid)->first();
            $review_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.review')->where('reviewapprove.req_recid', $req_recid)->first();

            $review1_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.second_review')->where('reviewapprove.req_recid', $req_recid)->first();

            $budgetowner_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.budget_owner')->where('reviewapprove.req_recid', $req_recid)->first();
            $approve_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.approve')->where('reviewapprove.req_recid', $req_recid)->first();
            $insuficient = Tasklist::where('tasklist.req_recid', $req_recid)->where('insufficient', 'Y')->first();
            $pending_at = Tasklist::where('req_recid', $req_recid)->first();
            $pending_at_team = '';
            if ($pending_at->next_checker_group == 'accounting') {
                $pending_at_team = 'Accounting & Finance';
            }
            $pending_at = Tasklist::join('users', 'users.email', 'tasklist.next_checker_group')->where('tasklist.req_recid', $req_recid)->first();

            $group_final = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_ACCOUNTING')->where('groupid.status',1)->get();

            if ($requester == '1' or !empty($condition_view)) {
                $group_requester = DB::table('groupid')
                    ->join('usermgt', 'groupid.email', 'usermgt.email')
                    ->where('groupid.group_id', $checker->group_id)
                    ->where('groupid.email', '!=', Auth::user()->email)
                    ->where('groupid.role_id', '!=', '1')
                    ->get();
                if (empty($group_requester)) {
                    Session::flash('error', 'No reviewer or approver found');
                    return redirect()->back();
                }

                $within_ornot = Paymentbody::where(['req_recid' => $req_recid, 'within_budget_code' => 'N'])->first();

                $total_all = Paymentbody::where('req_recid', $req_recid)->get();

                $total_spent_all = [];
                $total_spent = 0;
                foreach ($total_all as $key => $value) {
                    $total_spent += $value->total;
                }

                if ($total_spent <= 10000) {
                    $max_spent = '<=10000';
                } elseif ($total_spent > 10000 and $total_spent <= 50000) {
                    $max_spent = '<=50000';
                } else {
                    $max_spent = '>50000';
                }

                if (!empty($within_ornot)) {
                    if ($max_spent == '<=10000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')->where('groupid.status',1)->get();
                    } elseif ($max_spent == '<=50000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_MEMBER_EXCO')->where('groupid.status',1)->get();
                    } else {
                        $group_approver = 'CFO_ONLY';
                    }
                } else {
                    if ($max_spent == '<=10000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')->where('groupid.status',1)->get();
                    } elseif ($max_spent == '<=50000') {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.group_id', 'GROUP_MEMBER_EXCO')->where('groupid.status',1)->get();
                    } else {
                        $group_approver = 'MDOFFICE_ONLY';
                    }
                }

                $pr_id = $req_recid;

                // End First Submit Select reviewer
                return view('approver.payment', compact(
                                                    'budget_his', 
                                                    'pr_id', 
                                                    'top', 
                                                    'top_mid', 
                                                    'body', 
                                                    'bottom', 
                                                    'document', 
                                                    'description_response', 
                                                    'auditlog', 
                                                    'group_requester', 
                                                    'requester', 
                                                    'condition_view', 
                                                    'review', 
                                                    'approve', 
                                                    'request_status', 
                                                    'budget_code', 
                                                    'alternative_budget_codes', 
                                                    'dep_code', 
                                                    'group_approver', 
                                                    'group_final', 
                                                    'query', 
                                                    'total_spent', 
                                                    'body_bottom', 
                                                    'pending_at_team',
                                                    'totalAndYTD',
                                                    'budgetcode_na'
                                                ));
            } else {
                $pr_id = $req_recid;
                $tasklist = Tasklist::where('req_recid', $req_recid)->first();
                $owner = $tasklist->req_email;
                $within_budget = $tasklist->within_budget;
                $step_number = $tasklist->step_number;

                $final_res = 'N';
                $final = Reviewapprove::where('final', Auth::user()->email)->first();
                $final_checker = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('step_number', '>=', 3)->first();

                $last_step = $tasklist->step_number;
                if (!empty($final) and !empty($final_checker)) {
                    $final_res = 'Y';
                }

                $pdf = PDF::loadView('approver.paymentpdfform', compact(
                                                                    'insuficient', 
                                                                    'pr_id', 
                                                                    'request_status', 
                                                                    'top', 
                                                                    'top_mid', 
                                                                    'body', 
                                                                    'bottom', 
                                                                    'document', 
                                                                    'description_response', 
                                                                    'auditlog', 
                                                                    'requester', 
                                                                    'review', 
                                                                    'approve', 
                                                                    'final_res', 
                                                                    'query', 
                                                                    'total_spent', 
                                                                    'body_bottom', 
                                                                    'review_progress', 
                                                                    'review1_progress', 
                                                                    'budgetowner_progress', 
                                                                    'approve_progress', 
                                                                    'pending_at', 
                                                                    'requester_progress', 
                                                                    'final_progress', 
                                                                    'pending_at_team', 
                                                                    'alternative_budget_codes',
                                                                    'totalAndYTD',
                                                                    'budgetcode_na'
                                                                ));
                return $pdf->download($req_recid . '.pdf');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function downloadExcel()
    {
        $path = public_path('/static/template/payment-template.xlsx');
        return response()->download($path);
    }
    public function getBudgetNA($budget_codes){
        $budget_code = $budget_codes->toArray();
        $ifNull = [];
        foreach($budget_code as $data){
            if($data == null){
                $data = 'NA';
            }
            array_push($ifNull,$data);
        }
        if (count(array_flip($ifNull)) === 1 && end($ifNull) === 'NA') {
            $data_na = "Y";
        }else{
            $data_na = "N";
        }
        return $data_na;
    }
    private function transformApprover($approver)
    {
        if (!$approver) {
            return (object)(['email' => null, 'role' => null,]);
        }

        $approvers = explode('/', $approver);
        return (object)(['email' => $approvers[0], 'role' => $approvers[1],]);
    }
}