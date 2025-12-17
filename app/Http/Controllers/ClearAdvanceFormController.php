<?php

namespace App\Http\Controllers;

use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Models\AdvanceForm;
use App\Models\AdvanceFormDetail;
use App\Models\AllocateProduct;
use App\Models\AllocateSegment;
use App\Models\Auditlog;
use App\Models\Branchcode;
use App\Models\Budgetcode;
use App\Models\ClearAdvanceForm;
use App\Models\ClearAdvanceFormDetail;
use App\Models\Documentupload;
use App\Models\Requester;
use App\Models\Reviewapprove;
use App\Models\Tasklist;
use App\Models\User;
use App\Models\BudgetDetail;
use App\Traits\Currency;
use Carbon\Carbon;
use Exception;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;
use App\Enums\ActionEnum;

class ClearAdvanceFormController extends Controller
{
    use Currency;

    public function index()
    {
        try {
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
                ->where('tasklist.req_type', FormTypeEnum::ClearAdvanceFormRequest())
                ->where('tasklist.req_email', $user->email)
                ->where('tasklist.req_status', RequestStatusEnum::Save())
                ->orderBy('created_at', 'DESC')
                ->get();
            return view('clear_advance_form.index', compact('result'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            $user = Auth::user();

            $department  = $user->department;
            $budget_code = Budgetcode::orderByRaw("CASE
                                                    WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                        ELSE 1
                                                    END")
                                                ->orderBy('budget_code', 'desc')
                                                ->get();
            $alternative_budget_codes = Budgetcode::whereNotIn('budget_code', ['NA', 'NO'])->orderBy('budget_code', 'desc')->get();
            $dep_code = Branchcode::orderBy('branch_code', 'desc')->get();

            $advanceReferences = Tasklist::join('advance_form_details', 'advance_form_details.req_recid', 'tasklist.req_recid')
                ->where('tasklist.req_type', FormTypeEnum::AdvanceFormRequest())
                ->where('tasklist.req_status', '005')
                ->whereNull('advance_form_details.used_by_request')
                ->select('advance_form_details.req_recid')
                ->groupby('advance_form_details.req_recid')
                ->get();

            return view('clear_advance_form.create', compact(
                'budget_code',
                'alternative_budget_codes',
                'dep_code',
                'advanceReferences',
                'department'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function showWithAdvanceReferences($cryptedString, $references)
    {
        try {
            $reqRecids = explode(',', $references);

            /** Make sure all procurement has the same currency */
            $requesters = Requester::whereIn('req_recid', $reqRecids)->select('ccy')->groupby('ccy')->get();
            if (count($requesters) > 1) {
                Session::flash('error', 'Your procurement reference has difference currency.');
                return redirect()->back();
            }

            /** find one advance form to get defaule paid for and for  */
            $reqRecid = $reqRecids[0];
            $advanceForm = AdvanceForm::firstWhere('req_recid', $reqRecid);
            if (!$advanceForm) {
                Session::flash('error', "Cannot find advance form for ref : {$reqRecid}");
                return redirect()->back();
            }

            /** get all advance detial body */
            $advanceDetials = AdvanceFormDetail::whereIn('req_recid', $reqRecids)
                ->whereNull('used_by_request')
                ->get();
                  /**get budget detail */
            $budgetCodes = collect($advanceDetials)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */
            if (count($advanceDetials) == 0) {
                Session::flash('error', 'Your advance is already cleared. Please try again later.');
                return redirect()->back();
            }

            /** sub total amount use in advance detail */
            $totalAdvanceAmountUSD = collect($advanceDetials)->sum('total_amount_usd');
            $totalAdvanceAmountKHR = collect($advanceDetials)->sum('total_amount_khr');

            /** transform to advance form details */
            $details = collect($advanceDetials)->transform(function ($advance) {
                return [
                    'advance_detail_id'       => $advance->id,
                    'req_recid'               => $advance->req_recid,
                    'invoice_number'          => $advance->invoice_number,
                    'description'             => $advance->description,
                    'department_code'         => $advance->department_code,
                    'unit'                    => $advance->unit,
                    'quantity'                => $advance->quantity,
                    'unit_price_usd'          => $advance->unit_price_usd,
                    'total_amount_usd'        => $advance->total_amount_usd,
                    'unit_price_khr'          => $advance->unit_price_khr,
                    'total_amount_khr'        => $advance->total_amount_khr,
                    'budget_code'             => $advance->budget_code,
                    'alternative_budget_code' => $advance->alternative_budget_code,
                    'within_budget'           => $advance->within_budget,
                    'vat_item'                => $advance->vat_item,
                    'vat_item_khr'            => $advance->vat_item_khr
                ];
            });


            $user = Auth::user();
            $department = $user->department;

            $requester = (object)$requesters[0];
            $currency  = $requester->ccy;

            $advanceFormDetails = collect($details)->transform(function ($detail) {
                return (object)$detail;
            });

            return view('clear_advance_form.advanceRef', compact(
                'references',
                'currency',
                'department',
                'advanceFormDetails',
                'totalAdvanceAmountUSD',
                'totalAdvanceAmountKHR',
                'advanceForm',
                'totalAndYTD',
                'budgetcode_na'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please try again or contact administrator.');
            return redirect()->back();
        }
    }

    public function saveDraft(Request $request)
    {
        // allocate product segment less 100
        $allocate_product = $request->product_general + $request->product_loan_general  + $request->product_mortgage + $request->product_business + $request->product_personal + $request->product_card_general + $request->product_debit_card + $request->product_credit_card +
            $request->product_trade_general + $request->product_bank_guarantee + $request->product_letter_of_credit + $request->product_deposit_general +
            $request->product_casa_individual + $request->product_td_individual + $request->product_casa_corporate + $request->product_td_corporate;
        $allocate_segment = $request->segment_general + $request->segment_bfs + $request->segment_rfs + $request->segment_pb + $request->segment_pcp + $request->segment_afs;
       
        if ($allocate_product !== 100 or $allocate_segment !== 100) {
            Session::flash('error', 'Sum of value Product and Sagment must be 100');
            return redirect()->back();
        }
        // end
        /** make sure budget code at lease has one item */
        if (count($request->department_codes) == 0) {
            Session::flash('error', 'We have no item to save.');
            return redirect()->back();
        }

        /** validate departmetn codes and budget code */
        if (count($request->budget_codes) != count($request->department_codes)) {
            Session::flash('error', 'please make sure your select department and budget code');
            return redirect()->back();
        }

        /** check if that advance item is already used by other request */
        if ($request->has('advance_detail_ids')) {
            $advanceDetailsIds = $request->advance_detail_ids;
            $isAlreadyUsed = AdvanceFormDetail::whereIn('id', $advanceDetailsIds)->whereNotNull('used_by_request')->first();
            if ($isAlreadyUsed) {
                Session::flash('error', 'Your avance form item has been used by other request, Please try again later.');
                return redirect()->back();
            }
        }

        try {
            $currency = $request->currency;

            /** find discoutn amount */
            $discountAmountUSD = $this->getUSDAmount($request->discount, $currency);
            $discountAmountKHR = $this->getKHRAmount($request->discount, $currency);

            /** find VAT amount */
            $vatAmountUSD = $this->getUSDAmount($request->vat, $currency);
            $vatAmountKHR = $this->getKHRAmount($request->vat, $currency);

            /** calculate WHT amount */
            $whtAmountUSD = $this->getUSDAmount($request->wht, $currency);
            $whtAmountKHR = $this->getKHRAmount($request->wht, $currency);

            /**@var ClearAdvanceForm $clearAdvanceForm*/
            $clearAdvanceForm = ClearAdvanceForm::create([
                'department'                        => $request->department,
                'request_date'                      => Carbon::now(),
                'due_date'                          => $request->due_date,
                'currency'                          => $currency,
                'category'                          => $request->category[0],
                'advance_ref_no'                    => $request->reference_number,
                'subject'                           => $request->subject,
                'bank_name'                         => $request->bank_name,
                'account_name'                      => $request->account_name,
                'account_number'                    => $request->account_number,
                'bank_address'                      => $request->bank_address,
                'phone_number'                      => $request->phone_number,
                'company_name'                      => $request->company,
                'id_number'                         => $request->id_no,
                'contact_number'                    => $request->contact_no,
                'address'                           => $request->address,
                'additional_remark'                 => $request->additional_remarks,
                'additional_remark_product_segment' => $request->remarks_product_segment,
                'discount_amount_usd'               => $discountAmountUSD,
                'discount_amount_khr'               => $discountAmountKHR,
                'vat_amount_usd'                    => $vatAmountUSD,
                'vat_amount_khr'                    => $vatAmountKHR,
                'wht_amount_usd'                    => $whtAmountUSD,
                'wht_amount_khr'                    => $whtAmountKHR,

            ]);
            $clearAdvanceForm->refresh();

            $success = DB::transaction(function () use ($request, $clearAdvanceForm) {
                /** check if user add avance form as references */
                $advanceDetailsIds = null;
                if ($request->has('advance_detail_ids')) {
                    $advanceDetailsIds = $request->advance_detail_ids;
                }
                $clearAdvanceForm->createDetails([
                    'descriptions'             => $request->descriptions,
                    'department_codes'         => $request->department_codes,
                    'budget_codes'             => $request->budget_codes,
                    'alternative_budget_codes' => $request->alternative_budget_codes,
                    'units'                    => $request->units,
                    'qtys'                     => $request->qtys,
                    'unit_prices'              => $request->unit_prices,
                    'invoices'                 => $request->invoices,
                    'advance_detail_ids'       => $advanceDetailsIds,
                    'currency'                 => $request->currency,
                    'vat_item'                 => $request->vat_item,
                ]);

                /** create allocate Product */
                AllocateProduct::create([
                    'req_recid'        => $clearAdvanceForm->req_recid,
                    'general'          => $request->product_general ?? 0,
                    'loan_general'     => $request->product_loan_general ?? 0,
                    'mortgage'         => $request->product_mortgage ?? 0,
                    'business'         => $request->product_business ?? 0,
                    'personal'         => $request->product_personal ?? 0,
                    'card_general'     => $request->product_card_general ?? 0,
                    'debit_card'       => $request->product_debit_card ?? 0,
                    'credit_card'      => $request->product_credit_card ?? 0,
                    'trade_general'    => $request->product_trade_general ?? 0,
                    'bank_general'     => $request->product_bank_guarantee ?? 0,
                    'letter_of_credit' => $request->product_letter_of_credit ?? 0,
                    'deposit_general'  => $request->product_deposit_general ?? 0,
                    'casa_individual'  => $request->product_casa_individual ?? 0,
                    'td_individual'    => $request->product_td_individual ?? 0,
                    'casa_corporate'   => $request->product_casa_corporate ?? 0,
                    'td_corporate'     => $request->product_td_corporate ?? 0,
                ]);

                /** create Allocate Segment */
                AllocateSegment::create([
                    'req_recid' => $clearAdvanceForm->req_recid,
                    'general'   => $request->segment_general ?? 0,
                    'bfs'       => $request->segment_bfs ?? 0,
                    'rfs_ex_pb' => $request->segment_rfs ?? 0,
                    'pb'        => $request->segment_pb ?? 0,
                    'pcp'        => $request->segment_pcp ?? 0,
                    'afs'        => $request->segment_afs ?? 0,
                ]);

                /** create tasklist */
                $currentUser = Auth::user();
                $tasklist = Tasklist::create([
                    'req_recid'          => $clearAdvanceForm->req_recid,
                    'req_email'          => $currentUser->email,
                    'req_name'           => "{$currentUser->firstname} {$currentUser->lastname}",
                    'req_branch'         => $currentUser->department,
                    'req_position'       => $currentUser->position,
                    'req_from'           => FormTypeEnum::ClearAdvanceFormRequest(),
                    'req_type'           => FormTypeEnum::ClearAdvanceFormRequest(),
                    'next_checker_group' => 1,
                    'next_checker_role'  => 1,
                    'step_number'        => 1,
                    'step_status'        => 1,
                    'req_status'         => RequestStatusEnum::Save(),
                    'req_date'           => Carbon::now()->toDayDateTimeString(),
                    'within_budget'      => 'NOT YET CALCULATE',
                    'is_new_flow'        => 1
                ]);

                /** save to requester */
                Requester::create([
                    'req_recid'    => $clearAdvanceForm->req_recid,
                    'req_email'    => $currentUser->email,
                    'req_name'     => "{$currentUser->firstname} {$currentUser->lastname}",
                    'req_branch'   => $currentUser->department,
                    'req_position' => $currentUser->position,
                    'req_from'     => FormTypeEnum::ClearAdvanceFormRequest(),
                    'req_date'     => Carbon::now()->toDayDateTimeString(),
                    'subject'      => $request->subject,
                    'ccy'          => $request->currency
                ]);

                /** upload file if hase */
                if ($request->hasFile('fileupload')) {
                    $req_recid = $clearAdvanceForm->req_recid;
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
                            'activity_form'     => FormTypeEnum::AdvanceFormRequest(),
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

            /** flag Advance detail when this form use advance form as reference */
            $clearAdvanceForm->blockAdvanceDetial();

            /** need to clear all davancea mount before submit */
            $clearAdvanceForm->clearAdvanceAmount();

            /**update total amount of advance form for helping when query report */
            $clearAdvanceForm->updateTotalAmount();
            $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Save());
            $clearAdvanceForm->updateWithinBudgetForTasklist();

            return Redirect::to('form/clear-advances/edit/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please contact aministration for check your issue.');
            return redirect()->back();
        }
    }

    public function deleteItem(Request $request)
    {
        try {
            /**check if current item is only one, if it is only one, we need to delete all reqeust form */
            ClearAdvanceFormDetail::firstWhere('id', $request->item_id)->delete();

            /** check if item all delted */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::firstWhere('req_recid', $request->update_req_recid);
            if (!$clearAdvanceFormDetail) {
                DB::transaction(function () use ($request) {
                    Documentupload::where('req_recid', $request->update_req_recid)->delete();
                    AllocateProduct::where('req_recid', $request->update_req_recid)->delete();
                    AllocateSegment::where('req_recid', $request->update_req_recid)->delete();
                    Tasklist::where('req_recid', $request->update_req_recid)->delete();
                    Requester::where('req_recid', $request->update_req_recid)->delete();
                    ClearAdvanceForm::where('req_recid', $request->update_req_recid)->delete();
                });
                return Redirect::to('form/clear-advances');
            }

            /**update total amount of advance form for helping when query report */
            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->update_req_recid);
            if ($clearAdvanceForm) {
                /** after reject form, it will flag procurment body item to free again*/
                $clearAdvanceForm->freeAdvanceDetails();

                $clearAdvanceForm->updateTotalAmount();
                $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Submit());
                $clearAdvanceForm->updateWithinBudgetForTasklist();
            }


            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function edit($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetails = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
            /**get budget detail */
            $budgetCodes = collect($clearAdvanceFormDetails)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find department codes */
            $departmentCodes = Branchcode::orderBy('branch_code', 'desc')->get();

            /** find budget codes */
            $budgetCodes = Budgetcode::orderByRaw("CASE
                                                    WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                        ELSE 1
                                                    END")
                                                ->orderBy('budget_code', 'desc')
                                                ->get();

            /** find alternative code */
            $altBudgetCodes = Budgetcode::whereNotIn('budget_code', ['NA', 'NO'])->get();


            /** find approvers */
            $approvalUsers = $clearAdvanceForm->getAllUserLevelForApproval();
            /**multi Review */
            $multiReviewer = $clearAdvanceForm->getAllUserLevelReviewer();

            /**@var User $user */
            $user = Auth::user();
            /**find request link */
            $request_id = $user->requestLink($clearAdvanceForm->advance_ref_no);
            return view('clear_advance_form.edit', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetails',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'departmentCodes',
                'budgetCodes',
                'altBudgetCodes',
                'approvalUsers',
                'request_id',
                'totalAndYTD',
                'budgetcode_na',
                'multiReviewer'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please contact aministration.');
            return redirect()->back();
        }
    }

    public function detail($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
            /**get budget detail */
            $budgetCodes = collect($clearAdvanceFormDetail)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find approvers */
            $approvalUsers = $clearAdvanceForm->getUserApprovalLevel();

            /**@var Tasklist $tasklist */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                ->select('tasklist.*', 'recordstatus.record_status_description')
                ->where('req_recid', $req_recid)
                ->first();

            /** find pending at */
            $pendingUser = $tasklist->getPendingUser();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','doer_role', 'doer_action')
                ->get();
            /**@var User $user */
            $user = Auth::user();
            /**find request link */
            $request_id = $user->requestLink($clearAdvanceForm->advance_ref_no);
            return view('clear_advance_form.detail', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetail',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'auditlogs',
                'approvalUsers',
                'tasklist',
                'pendingUser',
                'request_id',
                'totalAndYTD',
                'budgetcode_na'
            ));
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
                return Redirect::to('form/clear-advances/show-for-resubmitting/' . Crypt::encrypt($req_recid . '___no'));
            }

            if ($tasklist->isQuery()) {
                return Redirect::to('form/clear-advances/show-for-query/' . Crypt::encrypt($req_recid . '___no'));
            }

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
            /**get budget detail */
            $budgetCodes = collect($clearAdvanceFormDetail)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find approvers */
            $approvalUsers = $clearAdvanceForm->getUserApprovalLevel();

            /** find pending at */
            $pendingUser = $tasklist->getPendingUser();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','doer_role', 'doer_action')
                ->get();

            /** check if current pending user is approver level */
            $isApprover = false;
            $pendingLevel = collect($approvalUsers)->firstWhere('is_pending', true);
            if ($pendingLevel && $pendingLevel->checker == 'approver') {
                $isApprover = true;
            }
            /**@var User $user */
            $user = Auth::user();
            /**find request link */
            $request_id = $user->requestLink($clearAdvanceForm->advance_ref_no);
            return view('clear_advance_form.approval', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetail',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'auditlogs',
                'approvalUsers',
                'tasklist',
                'pendingUser',
                'isApprover',
                'request_id',
                 'totalAndYTD',
                'budgetcode_na'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function showForQuery($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
            /**get budget detail */
            $budgetCodes = collect($clearAdvanceFormDetail)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find approvers */
            $approvalUsers = $clearAdvanceForm->getUserApprovalLevel();

            /**@var Tasklist $tasklist */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                ->select('tasklist.*', 'recordstatus.record_status_description')
                ->where('req_recid', $req_recid)
                ->first();

            /** find pending at */
            $pendingUser = $tasklist->getPendingUser();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                ->get();
            /**@var User $user */
            $user = Auth::user();
            /**find request link */
            $request_id = $user->requestLink($clearAdvanceForm->advance_ref_no);
            return view('clear_advance_form.query', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetail',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'auditlogs',
                'approvalUsers',
                'tasklist',
                'pendingUser',
                'request_id',
                'totalAndYTD',
                'budgetcode_na'
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
            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', "We cannot find this request please contact administor.");
                return redirect()->back();
            }

            /** make sure that this advance form is pending to current user */
            /**@var User $user */
            $user = Auth::user();
            if (!$clearAdvanceForm->isPendingOnUser($user)) {
                return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
            }

            $success = DB::transaction(function () use ($request, $clearAdvanceForm, $user) {

                $actionAndRole = $clearAdvanceForm->checkroleForClearAdvamce($clearAdvanceForm);
                $doerRole = $actionAndRole[0];
                $doerAction = $actionAndRole[1];
                /** process approval */
                if ($request->activity == 'approve') {
                    $user->approveClearAdvanceForm($clearAdvanceForm, $request->comment, $doerRole, $doerAction);

                    /** flag all advance reference to cleared when clearAdvanceForm was approved completed */
                    $clearAdvanceForm->clearForReferenceAdvanceForm();
                }

                /** process reject */
                if ($request->activity == 'reject') {
                    $user->rejectClearAdvanceForm($clearAdvanceForm, $request->comment, $doerRole, 'Rejected');

                    /** free all advance request detail when user destroy all item*/
                    AdvanceFormDetail::where('used_by_request', $clearAdvanceForm->req_recid)->update([
                        'used_by_request' => null,
                    ]);
                      /**add amount to budget code after reject request*/
                      $clearAdvanceForm->addAmountToBudget();
                }

                /** process asssign back */
                if ($request->activity == 'assign_back') {
                    $user->assignClearAdvanceFormBack($clearAdvanceForm, $request->comment, $doerRole, 'Assigned Back');
                }

                /**process query */
                if ($request->activity == 'query') {
                    $user->queryClearAdvanceForm($clearAdvanceForm, $request->comment, $doerRole, 'Queried Back');
                }

                return $clearAdvanceForm;
            });

            /**send email */
            if ($success) {
                if ($request->activity == 'approve') {
                    $clearAdvanceForm->sendEmailFormHasBeenApproved($request->comment);
                }

                if ($request->activity == 'reject') {
                    $clearAdvanceForm->sendEmailFormHasBeenRejected($request->comment);
                }

                if ($request->activity == 'assign_back') {
                    $clearAdvanceForm->sendEmailFormHasBeenAssignedBack($request->comment);
                }

                if ($request->activity == 'query') {
                    $clearAdvanceForm->sendEmailFormHasBeenQueriedBack($request->comment);
                }
            }

            return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Please contact administor.");
            return redirect()->back();
        }
    }

    public function showReSubmitForm($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var ClearAdvanceForm $clearAdvanceForm*/
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
            /**get budget detail */
            $budgetCodes = collect($clearAdvanceFormDetail)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                    ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                    ->get();
            $budgetcode_na = $this->getBudgetNA($budgetCodes);
            /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

           /** find department codes */
           $departmentCodes = Branchcode::orderBy('branch_code', 'desc')->get();

           /** find budget codes */
           $budgetCodes = Budgetcode::orderByRaw("CASE
                                                    WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                                        ELSE 1
                                                    END")
                                                ->orderBy('budget_code', 'desc')
                                                ->get();

            /** find alternative code */
            $altBudgetCodes = Budgetcode::whereNotIn('budget_code', ['NA', 'NO'])->get();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','doer_role', 'doer_action','doer_role', 'doer_action')
                ->get();

            /** find requester */
            $tasklist = Tasklist::join('recordstatus', 'recordstatus.record_status_id', '=', 'tasklist.req_status')
                ->select('tasklist.*', 'recordstatus.record_status_description')
                ->where('req_recid', $req_recid)
                ->first();
            /**@var User $user */
            $user = Auth::user();
            /**find request link */
            $request_id = $user->requestLink($clearAdvanceForm->advance_ref_no);
            return view('clear_advance_form.resubmit', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetail',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'departmentCodes',
                'budgetCodes',
                'altBudgetCodes',
                'auditlogs',
                'tasklist',
                'request_id',
                'totalAndYTD',
                'budgetcode_na'
            ));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function exportFormToPDF($cryptedString)
    {
        try {
            $req_recid =  $this->getRequestIdFromCryptedString($cryptedString);

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            if (!$clearAdvanceForm) {
                Session::flash('error', 'Form not found!');
                return redirect()->back();
            }

            /** find advance form detail */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::where('req_recid', $req_recid)->get();
             /**get budget detail */
             $budgetCodes = collect($clearAdvanceFormDetail)->pluck('budget_code');
             $totalAndYTD = BudgetDetail::whereIn('budget_code',$budgetCodes)
                     ->select('budget_code','budget_item','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                     ->get();
             $budgetcode_na = $this->getBudgetNA($budgetCodes);
             /**End */

            /** find advance form product */
            $product = AllocateProduct::firstWhere('req_recid', $req_recid);

            /** find advance form segment */
            $segment = AllocateSegment::firstWhere('req_recid', $req_recid);

            /** find document uploads */
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $totalDocument = collect($documents)->count();

            /** find approvers */
            $approvalUsers = $clearAdvanceForm->getUserApprovalLevel();

            /** find requester */
            $tasklist = Tasklist::firstWhere('req_recid', $req_recid);

            /** find pending at */
            $pendingUser = $tasklist->getPendingUser();

            /** find request log */
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                ->where('auditlog.req_recid', $req_recid)
                ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','doer_role', 'doer_action')
                ->get();

            $pdf = PDF::loadView('clear_advance_form.exportPDF', compact(
                'clearAdvanceForm',
                'clearAdvanceFormDetail',
                'product',
                'segment',
                'documents',
                'totalDocument',
                'auditlogs',
                'approvalUsers',
                'tasklist',
                'pendingUser',
                'totalAndYTD',
                'budgetcode_na'
            ));
            return $pdf->download($req_recid . '.pdf');
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function updateItem(Request $request)
    {
        try {
            /** update advance item */
            $clearAdvanceFormDetail = ClearAdvanceFormDetail::firstWhere('id', $request->update_item_id);
            if (!$clearAdvanceFormDetail) {
                Session::flash('error', "Cannot update, Item not found for id: {$request->update_item_id}");
                return redirect()->back();
            }

            /** check id user want to update or delete that item */
            if ($request->activity == 'update_item') {
                
                DB::transaction(function () use ($request, $clearAdvanceFormDetail) {
                    $taskList = Tasklist::where('req_recid',$request->update_req_recid)->first();
                    
                    /** update item */
                    $currency = $request->currency;
                    $quantity = $request->item_qty;
                    $unitPrice = $request->item_unit_price;
                    $vat_item = $request->item_vat;
                    $amount = ((float)$quantity * (float)$unitPrice) + (float) $vat_item;

                    $totalAmountUSD = $this->getUSDAmount($amount, $currency);
                    $totalAmountKHR = $this->getKHRAmount($amount, $currency);

                    $unitPriceUSD = $this->getUSDAmount($unitPrice, $currency);
                    $unitPriceKHR = $this->getKHRAmount($unitPrice, $currency);

                    $vatUSD = $this->getUSDAmount($vat_item, $currency);
                    $vatKHR = $this->getKHRAmount($vat_item, $currency);
                    // check budgetcode update or not
                    $new_budget_code = Budgetcode::where('budget_code',$request->item_budget_code)->first();
                    if($clearAdvanceFormDetail->budget_code == $new_budget_code->budget_code){
                        $budget_code = $clearAdvanceFormDetail->old_payment_remaining;
                        if($clearAdvanceFormDetail->total_amount_usd != $totalAmountUSD and $taskList->req_status != '001'){
                            $total = (float)$new_budget_code->payment_remaining + (float)$clearAdvanceFormDetail->total_amount_usd - (float)$totalAmountUSD;
                            $new_budget_code->update([
                                'payment'           => $total,
                                'temp_payment'      => $total,
                                'payment_remaining' => $total,
                            ]);
                        }
                    }else{
                        $budget_code = $new_budget_code->payment_remaining;
                        $oldBudget = Budgetcode::where('budget_code',$clearAdvanceFormDetail->budget_code)->first();
                        if($oldBudget and $taskList->req_status != '001'){
                            $total = (float)$oldBudget->payment_remaining + (float)$clearAdvanceFormDetail->total_amount_usd;
                            $new_budget_code->update([
                                'payment'           => $total,
                                'temp_payment'      => $total,
                                'payment_remaining' => $total,
                            ]);
                        }
                        if($new_budget_code and $taskList->req_status != '001'){
                            $total = (float)$new_budget_code->payment_remaining - (float)$totalAmountUSD;
                            $new_budget_code->update([
                                'payment'           => $total,
                                'temp_payment'      => $total,
                                'payment_remaining' => $total,
                            ]);
                        }
                    }
                    

                    $clearAdvanceFormDetail->update([
                        'invoice_number'          => $request->invoice_no,
                        'description'             => $request->item_description,
                        'department_code'         => $request->item_department_code,
                        'budget_code'             => $request->item_budget_code,
                        'alternative_budget_code' => $request->item_alternative_budget_code,
                        'unit'                    => $request->item_unit,
                        'quantity'                => $request->item_qty,
                        'exchange_rate_khr'       => $this->currentExchangeRate(),
                        'unit_price_usd'          => $unitPriceUSD,
                        'total_amount_usd'        => $totalAmountUSD,
                        'unit_price_khr'          => $unitPriceKHR,
                        'vat_item'                => $vatUSD,
                        'vat_item_khr'            => $vatKHR,
                        'total_amount_khr'        => $totalAmountKHR,
                        'old_payment_remaining'   => $budget_code
                    ]);

                    /**update total amount of advance form for helping when query report */
                    /**@var ClearAdvanceForm $clearAdvanceForm */
                    $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->update_req_recid);
                    if ($clearAdvanceForm) {
                        $clearAdvanceForm->updateTotalAmount();
                        if($taskList->step_number == 1){
                            $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Save());
                        }else{
                        $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Submit());
                        }
                        $clearAdvanceForm->updateWithinBudgetForTasklist();
                    }
                });
            }

            if ($request->activity == 'delete_item') {
                $clearAdvanceBeforeUpdate = ClearAdvanceFormDetail::firstWhere('id', $request->update_item_id);
                /**check if current item is only one, if it is only one, we need to delete all reqeust form */
                ClearAdvanceFormDetail::firstWhere('id', $request->update_item_id)->delete();

                /** check if item all delted */
                $clearAdvanceFormDetail = ClearAdvanceFormDetail::firstWhere('req_recid', $request->update_req_recid);
                if (!$clearAdvanceFormDetail) {
                    DB::transaction(function () use ($request, $clearAdvanceFormDetail) {
                       
                        Documentupload::where('req_recid', $request->update_req_recid)->delete();
                        AllocateProduct::where('req_recid', $request->update_req_recid)->delete();
                        AllocateSegment::where('req_recid', $request->update_req_recid)->delete();
                        Tasklist::where('req_recid', $request->update_req_recid)->delete();
                        Requester::where('req_recid', $request->update_req_recid)->delete();
                        ClearAdvanceForm::where('req_recid', $request->update_req_recid)->delete();
                        
                        
                        /** remove advance amount from budget code when request has been removed */
                        $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->update_req_recid);
                        if ($clearAdvanceForm) {
                            $clearAdvanceForm->updateBudgetCodeForReferenceAdvanceForm();
                        }

                        /** free all advance request detail when user destroy all item*/
                        AdvanceFormDetail::where('used_by_request', $request->update_req_recid)->update([
                            'used_by_request' => null,
                        ]);
                    });
                    return Redirect::to('form/clear-advances');
                }
                /**add budget code */
                $taskList = Tasklist::where('req_recid',$request->update_req_recid)->first();
                $new_budget_code = Budgetcode::where('budget_code',$request->item_budget_code)->first();
                if($new_budget_code and $taskList->req_status != '001'){
                    $total = (float)$new_budget_code->payment_remaining + (float)$clearAdvanceBeforeUpdate->total_amount_usd;
                    $new_budget_code->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
                }
                /**update total amount of advance form for helping when query report */
                /**@var ClearAdvanceForm $clearAdvanceForm */
                $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->update_req_recid);
                if ($clearAdvanceForm) {
                    $clearAdvanceForm->updateTotalAmount();
                    $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Save());
                    $clearAdvanceForm->updateWithinBudgetForTasklist();
                }
            }

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Someting went wrong please contact administrator.");
            return redirect()->back();
        }
    }

    public function resubmitForm(Request $request)
    {
        // allocate product segment less 100
        $allocate_product = $request->product_general + $request->product_loan_general  + $request->product_mortgage + $request->product_business + $request->product_personal + $request->product_card_general + $request->product_debit_card + $request->product_credit_card +
            $request->product_trade_general + $request->product_bank_guarantee + $request->product_letter_of_credit + $request->product_deposit_general +
            $request->product_casa_individual + $request->product_td_individual + $request->product_casa_corporate + $request->product_td_corporate;
        $allocate_segment = $request->segment_general + $request->segment_bfs + $request->segment_rfs + $request->segment_pb + $request->segment_pcp + $request->segment_afs;
        if ($allocate_product !== 100 or $allocate_segment !== 100) {
            Session::flash('error', 'Sum of value Product and Sagment must be 100');
            return redirect()->back();
        }
        // end

        /**@var ClearAdvanceForm $clearAdvanceForm */
        $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->req_recid);
        if (!$clearAdvanceForm) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }

        /**@var User $user */
        $user = Auth::user();
        if (!$user->isBelongToClearAdvanceForm($clearAdvanceForm)) {
            Session::flash('error', "Make sure you are requester to submit.");
            return redirect()->back();
        }

        /** check if user has permission to submit request */
        try {
            $success = DB::transaction(function () use ($clearAdvanceForm, $request) {
                $currency = $request->currency;
                /**Convert string to date */
                $date = Carbon::createFromFormat('Y-m-d', $request->due_date)->format('Y-m-d');
                /** find discoutn amount */
                $discountAmountUSD = $this->getUSDAmount($request->discount, $currency);
                $discountAmountKHR = $this->getKHRAmount($request->discount, $currency);

                /** find VAT amount */
                $vatAmountUSD = $this->getUSDAmount($request->vat, $currency);
                $vatAmountKHR = $this->getKHRAmount($request->vat, $currency);

                /** calculate WHT amount */
                $whtAmountUSD = $this->getUSDAmount($request->wht, $currency);
                $whtAmountKHR = $this->getKHRAmount($request->wht, $currency);

                /**@var ClearAdvanceForm $clearAdvanceForm*/
                $clearAdvanceForm->update([
                    'department'                        => $request->department,
                    'request_date'                      => Carbon::now(),
                    'due_date'                          => $date,
                    'currency'                          => $currency,
                    'category'                          => $request->category[0],
                    'advance_ref_no'                    => $request->reference_number,
                    'subject'                           => $request->subject,
                    'bank_name'                         => $request->bank_name,
                    'account_name'                      => $request->account_name,
                    'account_number'                    => $request->account_number,
                    'bank_address'                      => $request->bank_address,
                    'phone_number'                      => $request->phone_number,
                    'company_name'                      => $request->company,
                    'id_number'                         => $request->id_no,
                    'contact_number'                    => $request->contact_no,
                    'address'                           => $request->address,
                    'additional_remark'                 => $request->additional_remarks,
                    'additional_remark_product_segment' => $request->remarks_product_segment,
                    'discount_amount_usd'               => $discountAmountUSD,
                    'discount_amount_khr'               => $discountAmountKHR,
                    'vat_amount_usd'                    => $vatAmountUSD,
                    'vat_amount_khr'                    => $vatAmountKHR,
                    'wht_amount_usd'                    => $whtAmountUSD,
                    'wht_amount_khr'                    => $whtAmountKHR,

                ]);

                /** create product  */
                AllocateProduct::where('req_recid', $clearAdvanceForm->req_recid)
                    ->update([
                        'general'          => $request->product_general ?? 0,
                        'loan_general'     => $request->product_loan_general ?? 0,
                        'mortgage'         => $request->product_mortgage ?? 0,
                        'business'         => $request->product_business ?? 0,
                        'personal'         => $request->product_personal ?? 0,
                        'card_general'     => $request->product_card_general ?? 0,
                        'debit_card'       => $request->product_debit_card ?? 0,
                        'credit_card'      => $request->product_credit_card ?? 0,
                        'trade_general'    => $request->product_trade_general ?? 0,
                        'bank_general'     => $request->product_bank_guarantee ?? 0,
                        'letter_of_credit' => $request->product_letter_of_credit ?? 0,
                        'deposit_general'  => $request->product_deposit_general ?? 0,
                        'casa_individual'  => $request->product_casa_individual ?? 0,
                        'td_individual'    => $request->product_td_individual ?? 0,
                        'casa_corporate'   => $request->product_casa_corporate ?? 0,
                        'td_corporate'     => $request->product_td_corporate ?? 0,
                    ]);

                /** create advance form segment */
                AllocateSegment::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'general'   => $request->segment_general ?? 0,
                    'bfs'       => $request->segment_bfs ?? 0,
                    'rfs_ex_pb' => $request->segment_rfs ?? 0,
                    'pb'        => $request->segment_pb ?? 0,
                    'pcp'        => $request->segment_pcp ?? 0,
                    'afs'        => $request->segment_afs ?? 0,
                ]);

                /** process with upload  */
                $currentUser = Auth::user();
                $attach_remove = $request->att_remove;
                if (!empty($attach_remove)) {
                    $att_delete = explode(',', $attach_remove);
                    Documentupload::whereIn('id', $att_delete)->delete();
                }

                if ($request->hasFile('fileupload')) {
                    $req_recid = $clearAdvanceForm->req_recid;
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
                            'activity_form'     => FormTypeEnum::AdvanceFormRequest(),
                            'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                            'activity_datetime' => $date_time,
                        ]);
                    }
                }

                /** update task list */
                /**@var Tasklist $taskList*/
                $taskList = Tasklist::firstWhere('req_recid', $clearAdvanceForm->req_recid);
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
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
                    'req_recid'            => $clearAdvanceForm->req_recid,
                    'doer_email'           => $currentUser->email,
                    'doer_name'            => "{$currentUser->firstname} {$currentUser->lastname}",
                    'doer_branch'          => $currentUser->department,
                    'doer_position'        => $currentUser->position,
                    'activity_code'        => ActivityCodeEnum::Resubmitted(),
                    'activity_description' => $request->comment,
                    'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                    'doer_role'            => 'Requester',
                    'doer_action'          => 'Resubmitted'
                ]);

                /** update budget code after submit request */
                $clearAdvanceForm->updateTotalAmount();
                $clearAdvanceForm->updateBudgetCodeAfterResubmit();
                return $clearAdvanceForm;
            });

            /** send email */
            if ($success) {
                $clearAdvanceForm->sendEmailToPendingUser($request->comment);
            }

            return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot submit this request . Please contact administrator.");
            return redirect()->back();
        }
    }

    public function queryBackToApprover(Request $request)
    {
        try {
            /** make sure curren trequest is need to be query */
            $taskList =  Tasklist::firstWhere('req_recid', $request->req_recid);
            if (!$taskList->isQuery()) {
                return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($request->req_recid . '___no'));
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
                    'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                    'doer_role'            => 'Requester',
                    'doer_action'          => 'Queried Back'
                ]);
            });

            return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($request->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot query back this request . Please contact administrator.");
            return redirect()->back();
        }
    }


    public function addNewItem(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $currency = $request->new_currency;
                $quantity = $request->new_qty;
                $unitPrice = $request->new_unit_price;
                $vat_item = $request->vat_item;
                $amount = ((float)$quantity * (float)$unitPrice) + (float) $vat_item;

                $totalAmountUSD = $this->getUSDAmount($amount, $currency);
                $totalAmountKHR = $this->getKHRAmount($amount, $currency);

                $unitPriceUSD = $this->getUSDAmount($unitPrice, $currency);
                $unitPriceKHR = $this->getKHRAmount($unitPrice, $currency);

                $vatUSD = $this->getUSDAmount($vat_item, $currency);
                $vatKHR = $this->getKHRAmount($vat_item, $currency);
                $payment_remaining = Budgetcode::where('budget_code', $request->new_budget_code)->first();

                ClearAdvanceFormDetail::create([
                    'invoice_number'          => $request->invoice_no,
                    'req_recid'               => $request->new_req_recid,
                    'description'             => $request->new_description,
                    'department_code'         => $request->new_department_code,
                    'budget_code'             => $request->new_budget_code,
                    'alternative_budget_code' => $request->new_alternative_budget_code,
                    'unit'                    => $request->new_unit,
                    'quantity'                => $request->new_qty,
                    'exchange_rate_khr'       => $this->currentExchangeRate(),
                    'unit_price_usd'          => $unitPriceUSD,
                    'total_amount_usd'        => $totalAmountUSD,
                    'unit_price_khr'          => $unitPriceKHR,
                    'vat_item'                => $vatUSD,
                    'vat_item_khr'            => $vatKHR,
                    'total_amount_khr'        => $totalAmountKHR,
                    'within_budget'           => 'NOT YET CALCULATED',
                    'old_payment_remaining'   => $payment_remaining->payment_remaining,
                ]);

                /**update total amount of advance form for helping when query report */
                /**@var ClearAdvanceForm $clearAdvanceForm */
                $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->new_req_recid);
                if ($clearAdvanceForm) {
                    $clearAdvanceForm->updateTotalAmount();
                    $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Save());
                    $clearAdvanceForm->updateWithinBudgetForTasklist();
                }
            });

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot create new item please contact your administator.");
            return redirect()->back();
        }
    }


    public function submitRequest(Request $request)
    {
        /**@var ClearAdvanceForm $clearAdvanceForm */
        $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $request->req_recid);
        if (!$clearAdvanceForm) {
            Session::flash('error', "Form not found. Please contact administrator.");
            return redirect()->back();
        }

        /** check if form already submit */
        if ($clearAdvanceForm->isAlreadySubmitted()) {
            return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
        }

        /**@var User $user */
        $user = Auth::user();
        if (!$user->isBelongToClearAdvanceForm($clearAdvanceForm)) {
            Session::flash('error', "Make sure you are requester to submit.");
            return redirect()->back();
        }
         /**if user saved request at least 6 days */
         $tasklist = Tasklist::where('req_recid', $request->req_recid)->first();
         if($tasklist->req_status == "001"){
             $now = Carbon::now()->toDateTimeString();  
             $interval = $tasklist->created_at->diffInDays($now); 
             if($interval > 6){
                 Session::flash('error', "You should submit your request after saved at least 6 days! Please delete this request and create one new.");
                 return redirect()->back();
             }
         }
        /** check if user has permission to submit request */
        try {
            $success = DB::transaction(function () use ($clearAdvanceForm, $request) {
                $currency = $request->currency;
                /**Convert string to date */
                $date = Carbon::createFromFormat('Y-m-d', $request->due_date)->format('Y-m-d');
                /** find discoutn amount */
                $discountAmountUSD = $this->getUSDAmount($request->discount, $currency);
                $discountAmountKHR = $this->getKHRAmount($request->discount, $currency);

                /** find VAT amount */
                $vatAmountUSD = $this->getUSDAmount($request->vat, $currency);
                $vatAmountKHR = $this->getKHRAmount($request->vat, $currency);

                /** calculate WHT amount */
                $whtAmountUSD = $this->getUSDAmount($request->wht, $currency);
                $whtAmountKHR = $this->getKHRAmount($request->wht, $currency);

                /**@var ClearAdvanceForm $clearAdvanceForm*/
                $clearAdvanceForm->update([
                    'department'                        => $request->department,
                    'request_date'                      => Carbon::now(),
                    'due_date'                          => $date,
                    'currency'                          => $currency,
                    'category'                          => $request->category[0],
                    'advance_ref_no'                    => $request->reference_number,
                    'subject'                           => $request->subject,
                    'bank_name'                         => $request->bank_name,
                    'account_name'                      => $request->account_name,
                    'account_number'                    => $request->account_number,
                    'bank_address'                      => $request->bank_address,
                    'phone_number'                      => $request->phone_number,
                    'company_name'                      => $request->company,
                    'id_number'                         => $request->id_no,
                    'contact_number'                    => $request->contact_no,
                    'address'                           => $request->address,
                    'additional_remark'                 => $request->additional_remarks,
                    'additional_remark_product_segment' => $request->remarks_product_segment,
                    'discount_amount_usd'               => $discountAmountUSD,
                    'discount_amount_khr'               => $discountAmountKHR,
                    'vat_amount_usd'                    => $vatAmountUSD,
                    'vat_amount_khr'                    => $vatAmountKHR,
                    'wht_amount_usd'                    => $whtAmountUSD,
                    'wht_amount_khr'                    => $whtAmountKHR,

                ]);

                /** update advance form product */
                AllocateProduct::firstWhere('req_recid', $request->req_recid)->update([
                    'general'          => $request->product_general ?? 0,
                    'loan_general'     => $request->product_loan_general ?? 0,
                    'mortgage'         => $request->product_mortgage ?? 0,
                    'business'         => $request->product_business ?? 0,
                    'personal'         => $request->product_personal ?? 0,
                    'card_general'     => $request->product_card_general ?? 0,
                    'debit_card'       => $request->product_debit_card ?? 0,
                    'credit_card'      => $request->product_credit_card ?? 0,
                    'trade_general'    => $request->product_trade_general ?? 0,
                    'bank_general'     => $request->product_bank_guarantee ?? 0,
                    'letter_of_credit' => $request->product_letter_of_credit ?? 0,
                    'deposit_general'  => $request->product_deposit_general ?? 0,
                    'casa_individual'  => $request->product_casa_individual ?? 0,
                    'td_individual'    => $request->product_td_individual ?? 0,
                    'casa_corporate'   => $request->product_casa_corporate ?? 0,
                    'td_corporate'     => $request->product_td_corporate ?? 0,
                ]);

                /** update advance form segment */
                AllocateSegment::firstWhere('req_recid', $request->req_recid)->update([
                    'general'   => $request->segment_general ?? 0,
                    'bfs'       => $request->segment_bfs ?? 0,
                    'rfs_ex_pb' => $request->segment_rfs ?? 0,
                    'pb'        => $request->segment_pb ?? 0,
                    'pcp'        => $request->segment_pcp ?? 0,
                    'afs'        => $request->segment_afs ?? 0,
                ]);
                /**update requester table */
                Requester::firstWhere('req_recid', $request->req_recid)->update([
                    'subject'   => $request->subject
                ]);
                /** process with upload  */
                $attach_remove = $request->att_remove;
                if (!empty($attach_remove)) {
                    $att_delete = explode(',', $attach_remove);
                    Documentupload::whereIn('id', $att_delete)->delete();
                }

                if ($request->hasFile('fileupload')) {
                    $currentUser = Auth::user();
                    $req_recid = $clearAdvanceForm->req_recid;
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
                            'activity_form'     => FormTypeEnum::AdvanceFormRequest(),
                            'uuid'              => Uuid::generate(5, $date_time . $file_name . $req_recid, Uuid::NS_DNS),
                            'activity_datetime' => $date_time,
                        ]);
                    }
                }

                /** process approval */
                /**base on flow user allowed to select only first reviewer and approver, and other is auto select */
                $clearAdvanceForm->saveApprovalLevel($request,$request->req_recid, $request->first_reviewer, $request->approver);

                /**update total amount of advance form for helping when query report */
                $clearAdvanceForm->updateTotalAmount();
                $clearAdvanceForm->updateFormDetailTotalaBudgetAmount(ActionEnum::Submit());
                $clearAdvanceForm->updateWithinBudgetForTasklist();

                /** update budget code after submit request */
                $clearAdvanceForm->updateTotalBudget();

                /** save log */
                $clearAdvanceForm->saveLog($request->comment,'Requester', 'Submitted');

                return $clearAdvanceForm;
            });

            /** send email */
            if ($success) {
                $clearAdvanceForm->sendEmailToPendingUser($request->comment);
            }

            return Redirect::to('form/clear-advances/detail/' . Crypt::encrypt($clearAdvanceForm->req_recid . '___no'));
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot submit this request . Please contact administrator.");
            return redirect()->back();
        }
    }

    public function previewYTDExpense(Request $request)
    {
        try {
            $req_recid = $request->req_recid;

            /**@var ClearAdvanceForm $clearAdvanceForm*/
            $clearAdvanceForm = ClearAdvanceForm::firstWhere('req_recid', $req_recid);
            $advanformPreviews = $clearAdvanceForm->previewBeforSubmit();

            $previewForms = collect($advanformPreviews)->map(function ($advanformPreview) {
                $advance = (object)$advanformPreview;

                $budgetCode             = $advance->budget_code;
                $alterNativeCode        = $advance->alternative_budget_code;
                $totalRequestAmount     = number_format($advance->total_request, 2);
                $totalYTDExpense        = number_format($advance->ytd_expense, 2);
                $totalBudgetAmount      = number_format($advance->total_budget, 2);
                $totalRemainingAmount   = number_format($advance->total_remaining_amount, 2);

                return [
                    'budget_code'             => $budgetCode,
                    'alternative_budget_code' => $alterNativeCode,
                    'total_request'           => $totalRequestAmount,
                    'total_budget'            => $totalBudgetAmount,
                    'ytd_expense'             => $totalYTDExpense,
                    'total_remaining_amount'  => $totalRemainingAmount,
                    'status'                  => $advance->status
                ];
            });

            return response(['reposnseCode' => '001', 'data' => $previewForms], 200);
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot submit this request . Please contact administrator.");
            return redirect()->back();
        }
    }

    public function deleteRequest(Request $request)
    {
        try {
            $req_recid = $request->req_recid;

            /**@var ClearAdvanceForm $clearAdvanceForm */
            $clearAdvanceForm = ClearAdvanceForm::where('req_recid', $req_recid)->first();
            if (!$clearAdvanceForm) {
                return Redirect::to('form/clear-advances');
            }

            DB::transaction(function () use ($req_recid, $clearAdvanceForm) {
                /** delete requester */
                Requester::where('req_recid', $req_recid)->delete();

                /** delete review approver */
                Reviewapprove::where('req_recid', $req_recid)->delete();

                /** task list */
                Tasklist::where('req_recid', $req_recid)->delete();

                /** upload */
                Documentupload::where('req_recid', $req_recid)->delete();

                /** advance form segment */
                AllocateSegment::where('req_recid', $req_recid)->delete();

                /** advance form product */
                AllocateProduct::where('req_recid', $req_recid)->delete();

                /** remove advance amount from budget code when request hase been removed */
                $clearAdvanceForm->updateBudgetCodeForReferenceAdvanceForm();

                /** free all advance request detail when user destroy all item*/
                AdvanceFormDetail::where('used_by_request', $req_recid)->update([
                    'used_by_request' => null,
                ]);

                /** advance form detail */
                ClearAdvanceFormDetail::where('req_recid', $req_recid)->delete();
                $clearAdvanceForm->delete();
            });

            return Redirect::to('form/clear-advances');
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', "Cannot delete that reqeust. Please contact administrator.");
            return redirect()->back();
        }
    }

    public function downloadExcel()
    {
        $path = public_path('/static/template/clear-advance-template.xlsx');
        return response()->download($path);
    }

    private function isTheSameBudgetOwner($budgetCodes)
    {
        $budgets = Budgetcode::whereIn('budget_code', $budgetCodes)->select('budget_owner')->groupBy('budget_owner')->get();
        if (collect($budgets)->count() > 1) {
            return false;
        }
        return true;
    }

    private function getRequestIdFromCryptedString($cryptedString)
    {
        $param_url = Crypt::decrypt($cryptedString);
        $after_split = explode('___', $param_url);
        return $after_split[0];
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
}
