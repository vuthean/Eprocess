<?php

namespace App\Http\Controllers;

use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Models\Auditlog;
use App\Models\Branchcode;
use App\Models\Budgetcode;
use App\Models\BudgetDetail;
use App\Models\Budgethistory;
use App\Models\Documentupload;
use App\Models\Emailgroup;
use App\Models\Flowconfig;
use App\Models\Groupid;
use App\Models\User;
use App\Models\Procurement;
use App\Models\Procurementbody;
use App\Models\Procurementbottom;
use App\Models\Procurementfooter;
use App\Models\Requester;
use App\Models\Reviewapprove;
use App\Models\Tasklist;
use App\Myclass\Defaultsave;
use App\Myclass\Sendemail;
use App\Models\ProcurementRecord;
use App\Models\BankPaymentVoucher;
use Carbon\Carbon;
use Dotenv\Loader\Loader;
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
use Illuminate\Support\Str;

class ProcurementController extends Controller
{
    public function listAuthRequest()
    {
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
            ->where('tasklist.req_type', 1) // number 1 is procurement form name
            ->where('tasklist.req_email', $user->email)
            ->where('tasklist.req_status', '001') //status 001 is saved/created
            ->get();

        return view('form.procurement_list', compact('result'));
    }
    public function getProcurementListingData(Request $request)
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
        $record_query = Tasklist::select(
            'tasklist.*',
            'formname.formname',
            'formname.description',
            'recordstatus.record_status_description',
            'requester.subject',
        )
            ->join('formname', 'tasklist.req_type', 'formname.id')
            ->leftJoin('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->leftJoin('requester', 'tasklist.req_recid', 'requester.req_recid')
            ->where('tasklist.req_type', 1) // number 1 is procurement form name
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
    public function listing()
    {
        /**@var User $user*/
        $user = Auth::user();

        /** make sure current user is allowed to view procurement records */
        if (!$user->isAllowToAccessProcurementRecord()) {
            return redirect()->back();
        }

        // $hasProcuredByEmails = $user->hasProcureByEmails();
        // if (!$hasProcuredByEmails) {
        //     return redirect()->back();
        // }
        // $result = $hasProcuredByEmails;

        Session::put('is_allow_procurement', true);
        return view('procurement.index');
    }
    public function getProcurementRequestListingData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = 'tasklist.updated_at'; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $group_receiver = '';
        if (Session::get('is_procurement') == '1') {
            $group_receiver = 'GROUP_PROCUREMENT';
        }
        elseif (Session::get('is_markating') == '1') {
            $group_receiver = 'GROUP_MARKETING';
        }
        if (Session::get('is_admin_team') == '1') {
            $group_receiver = 'GROUP_ADMINISTRATION';
        }
        elseif (Session::get('PLD_team') == '1') {
            $group_receiver = 'GROUP_LEARNING_PEOPLE';
        }
        elseif (Session::get('is_infra_team') == '1') {
            $group_receiver = 'GROUP_INFRA';
        }
        elseif (Session::get('is_alternative_team') == '1') {
            $group_receiver = 'GROUP_ACD';
        }
        $record_query = Procurementbody::select(
                                        'tasklist.updated_at',
                                        'requester.req_recid',
                                        'requester.req_name',
                                        'requester.req_branch',
                                        'requester.req_date',
                                        'formname.formname',
                                        'formname.description',
                                        'reviewapprove.final',
                                        'reviewapprove.final_group',
                                        'users.fullname',
                                        'requester.subject',
                                        DB::raw("(
                                        CASE 
                                            When sum(paid='Y') = 0 then 'Pending'
                                            When sum(paid='N') = 0 then 'Done'
                                            When sum(paid='Y') != 0 AND sum(paid='N') != 0 then 'Partially Done'
                                        END) AS status"))
                                        
                                        ->join('tasklist','tasklist.req_recid','procurementbody.req_recid')
                                        ->join('formname','formname.id','tasklist.req_type')
                                        ->join('reviewapprove','reviewapprove.req_recid','tasklist.req_recid')
                                        ->join('requester','requester.req_recid','tasklist.req_recid')
                                        ->join('users','reviewapprove.final','users.email')
                                        ->where('tasklist.req_status','005')
                                        ->where('final_group',$group_receiver)
                                        ->groupby('tasklist.updated_at');
        $totalRecords = $record_query->get()->count();
        $totalRecordswithFilter = $record_query->where(function ($query) use ($searchValue) {
            $query->where('procurementbody.req_recid', 'like', '%' . $searchValue . '%');
            $query->orwhere('requester.req_name', 'like', '%' . $searchValue . '%');
            $query->orwhere('users.fullname', 'like', '%' . $searchValue . '%');
            $query->orwhere('requester.subject', 'like', '%' . $searchValue . '%');
        })->get()->count();
        $records = $record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('procurementbody.req_recid', 'like', '%' . $searchValue . '%');
                $query->orwhere('requester.req_name', 'like', '%' . $searchValue . '%');
                $query->orwhere('users.fullname', 'like', '%' . $searchValue . '%');
                $query->orwhere('requester.subject', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $req_recid = '<a href="' . url($record->description . '/' . Crypt::encrypt($record->req_recid . '___' . 'yes')) . '">' . $record->req_recid . '</a>';
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
                "req_name" => $record->req_name,
                "req_branch" => $record->req_branch,
                "recieve_date" => Carbon::createFromFormat('Y-m-d H:i:s', $record->updated_at)->format('Y-m-d H:i a'),
                "procure_by" => $record->fullname,
                "payment_status" => $record->status,
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
    public function index()
    {
        try {
            $budget_code = Budgetcode::orderByRaw("CASE
                                          WHEN budget_code REGEXP '^[0-9]+$' THEN 0
                                          ELSE 1
                                      END")
                          ->orderBy('budget_code', 'desc')
                          ->get();   
            $alternative_budget_codes = Budgetcode::whereNotIn('budget_code', ['NA', 'NO'])->get();
            $dep_code = Branchcode::orderBy('branch_code', 'desc')->get();
            return view('form.procurement', compact('budget_code', 'dep_code', 'alternative_budget_codes'));
        } catch (\Exception $e) {
            Session::flash('error', 'Please Contact Admin');
            Log::info($e);
            return redirect()->back();
        }
    }

    public function procurementSave(Request $request)
    {
        $req_recid = $request->req_recid;
        // allocate product segment less 100
        $allocate_product = $request->general + $request->loan_general + $request->mortgage + $request->business + $request->personal + $request->card_general + $request->debit_card + $request->credit_card +
            $request->trade_general + $request->bank_guarantee + $request->letter_of_credit + $request->deposit_general +
            $request->casa_individual + $request->td_individual + $request->casa_corporate + $request->td_corporate;
        $allocate_segment = $request->general_segment + $request->bfs + $request->rfs + $request->pb + $request->pcp + $request->afs;
        // dd(  $allocate_segment);
        if ($allocate_product !== 100 or $allocate_segment !== 100) {
            Session::flash('error', 'Sum of value Product and Sagment must be 100');
            return redirect()->back();
        }
        // end
        if($request->vendor_name !== null){
            for ($i = 0; $i < count($request->vendor_name); $i++) {
                if (($request->vendor_name[$i] == null and $request->vendor_description[$i] != null) or ($request->vendor_name[$i] != null and $request->vendor_description[$i] == null)) {
                    Session::flash('error', 'Field Vender Name and Justification can not null!');
                    return redirect()->back();
                }
            }
        }
        
        try {
            if ($request->req_recid && $request->submit == 'submit') {
                $checker_mail = DB::transaction(function () use ($request, $req_recid) {
                    /**@var Tasklist $taskList*/
                    $taskList = Tasklist::firstWhere('req_recid', $req_recid);

                    if (!$taskList->isAssignedBack()) {
                        /** transform approver */
                        $firstReviewer    = $this->transformApprover($request->slc_review);
                        $secondReviewer   = $this->transformApprover($request->reviewer1);
                        $thirdReviewer    = $this->transformApprover($request->reviewer2);
                        $forthReviewer    = $this->transformApprover($request->reviewer3);
                        $approver         = $this->transformApprover($request->slc_approve);
                        $approver_co      = $this->transformApprover($request->slc_approve_co);
                        $finalApprover    = $this->transformApprover($request->slc_final);

                        $procurementBody = Procurementbody::firstWhere('req_recid', $req_recid);
                        $final_group = GroupId::join('groupdescription','groupdescription.group_id','groupid.group_id')
                                    ->where('groupid.email',$finalApprover->email)
                                    ->where('groupdescription.special','Y')
                                    ->where('groupdescription.is_procurement_record','Y')
                                    ->where('groupid.status',1)
                                    ->first();
                                
                        if ($procurementBody->isWithinBudget()) {
                            /** find budget owner */
                            $budgetDetail     = $this->findBudgetOwnerForProcurement($req_recid);
                            $budgetOwnerEmail = $budgetDetail->budget_owner;

                            /**@var Reviewapprove $reviewApprover */
                            $reviewApprover = Reviewapprove::firstOrNew(['req_recid' => $req_recid]);
                            $reviewApprover->req_recid     = $req_recid;
                            $reviewApprover->review        = $firstReviewer->email;
                            $reviewApprover->second_review = $secondReviewer->email;
                            $reviewApprover->third_review  = $thirdReviewer->email;
                            $reviewApprover->fourth_reviewer = $forthReviewer->email;
                            $reviewApprover->budget_owner  = $budgetOwnerEmail;
                            $reviewApprover->approve       = $approver->email;
                            $reviewApprover->co_approver    = $approver_co->email;
                            $reviewApprover->final         = $finalApprover->email;
                            $reviewApprover->final_group       = !empty($final_group->group_id)?$final_group->group_id:'';
                            $reviewApprover->accounting = 'accounting';
                            $reviewApprover->procurement = $finalApprover->email;
                            $reviewApprover->save();
                            $reviewApprover->refresh();
                        } else {
                            /**@var Reviewapprove $reviewApprover */
                            $reviewApprover = Reviewapprove::firstOrNew(['req_recid' => $req_recid]);
                            $reviewApprover->req_recid     = $req_recid;
                            $reviewApprover->review        = $firstReviewer->email;
                            $reviewApprover->second_review = $secondReviewer->email;
                            $reviewApprover->third_review  = $thirdReviewer->email;
                            $reviewApprover->fourth_reviewer = $forthReviewer->email;
                            $reviewApprover->budget_owner  = 'NA';
                            $reviewApprover->approve       = $approver->email;
                            $reviewApprover->co_approver    = $approver_co->email;
                            $reviewApprover->final         = $finalApprover->email;
                            $reviewApprover->final_group       = !empty($final_group->group_id)?$final_group->group_id:'';
                            $reviewApprover->accounting = 'accounting';
                            $reviewApprover->procurement = $finalApprover->email;
                            $reviewApprover->save();
                            $reviewApprover->refresh();
                        }

                        /** we allow to skip only first reviewer and second revieer */
                        $stepNumber = 0;
                        $roleId = 1;
                        if ($firstReviewer->email) {
                            $stepNumber = 1;
                            $roleId = $firstReviewer->role;
                        } elseif ($secondReviewer->email) {
                            $stepNumber = 2;
                            $roleId = $secondReviewer->role;
                        }elseif ($thirdReviewer->email) {
                            $stepNumber = 3;
                            $roleId = $thirdReviewer->role;
                        }elseif ($forthReviewer->email) {
                            $stepNumber = 4;
                            $roleId = $forthReviewer->role;
                        } else {
                            $stepNumber = 5;
                            $roleId = '2';
                        }

                        /**@var Procurement $procurement */
                        $procurement = Procurement::firstWhere('req_recid', $req_recid);
                        $approver = (object)$procurement->findApproverForStep($stepNumber, $procurementBody->within_budget_code);
                        $taskList->update([
                            'next_checker_group' => $approver->next_checker_group,
                            'next_checker_role'  => $roleId,
                            'within_budget'      => $procurementBody->within_budget_code,
                            'step_number'        => $approver->step_number,
                            'req_status'         => RequestStatusEnum::Pending()
                        ]);

                        /** create audit log */
                        Auditlog::create([
                            'req_recid'            => $req_recid,
                            'doer_email'           => $request->req_email,
                            'doer_name'            => $request->req_name,
                            'doer_branch'          => $request->req_department,
                            'doer_position'        => $request->req_position,
                            'activity_code'        => ActivityCodeEnum::Submitted(),
                            'activity_description' => $request->comment,
                            'activity_form'        => FormTypeEnum::ProcurementRequest(),
                            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                            'step_action'          => '1',
                            'doer_role'            => 'Requester',
                            'doer_action'          => 'Submitted Request'
                        ]);

                        $checker_mail = $approver->next_checker_group;
                    } else {
                        /**update task list */
                        $grand_total = $request->grand_total;
                        $grand_total_after_assign_bank = $taskList->old_amout_after_assign_bank;
                        $procurementBody = Procurementbody::firstWhere('req_recid', $req_recid);
                        $reviewApprove = Reviewapprove::firstWhere('req_recid', $req_recid);

                        if($grand_total != $grand_total_after_assign_bank and ($taskList->by_step == 9 or $taskList->by_step == 10)){
                            if(!$reviewApprove->review){
                                $next_reviewer = $reviewApprove->budget_owner;
                                $next_reviewer_step = 5;
                                $checker_mail = $reviewApprove->budget_owner;
                            }else{
                                $next_reviewer = $reviewApprove->review;
                                $next_reviewer_step = 1;
                                $checker_mail = $reviewApprove->review;
                            }
                             Tasklist::where('req_recid', $req_recid)->update([
                                'next_checker_group' => $next_reviewer,
                                'next_checker_role'  => 2,
                                'step_number'        => $next_reviewer_step,
                                'within_budget'      => $procurementBody->within_budget_code,
                                'assign_back_by'     => null,
                                'by_step'            => null,
                                'by_role'            => null,
                                'req_status'         => '002'
                            ]);
                            
                        }else{
                            Tasklist::where('req_recid', $req_recid)->update([
                                'next_checker_group' => $taskList->assign_back_by,
                                'next_checker_role'  => $taskList->by_role,
                                'step_number'        => $taskList->by_step,
                                'within_budget'      => $procurementBody->within_budget_code,
                                'assign_back_by'     => null,
                                'by_step'            => null,
                                'by_role'            => null,
                                'req_status'         => '002'
                            ]);
                            $checker_mail = $taskList->assign_back_by;
                        }

                        /** create audit log */
                        Auditlog::create([
                            'req_recid'            => $req_recid,
                            'doer_email'           => $request->req_email,
                            'doer_name'            => $request->req_name,
                            'doer_branch'          => $request->req_department,
                            'doer_position'        => $request->req_position,
                            'activity_code'        => ActivityCodeEnum::Resubmitted(),
                            'activity_description' => $request->comment,
                            'activity_form'        => FormTypeEnum::ProcurementRequest(),
                            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                            'step_action'          => '1',
                            'doer_role'            => 'Requester',
                            'doer_action'          => 'Resubmitted Request'
                        ]);
                        
                    }
                    
                    /** first or create new procurment */
                    $procurement = Procurement::firstOrNew(['req_recid' => $req_recid]);
                    $procurement->req_email     = $request->req_email;
                    $procurement->req_date      = date('d/m/y');
                    $procurement->purpose       = $request->purpose_rationale;
                    $procurement->bid           = $request->bid_waiver_sole;
                    $procurement->justification = $request->justification_for_request;
                    $procurement->vat           = $request->vat;
                    $procurement->save();
                    /** update procurement bottom */
                    Procurementbottom::where('req_recid', $req_recid)->update([
                        'general'          => $request->general,
                        'loan_general'     => $request->loan_general,
                        'mortage'          => $request->mortgage,
                        'busines'          => $request->business,
                        'personal'         => $request->personal,
                        'card_general'     => $request->card_general,
                        'debit_card'       => $request->debit_card,
                        'credit_card'      => $request->credit_card,
                        'trade_general'    => $request->trade_general,
                        'bank_guarantee'   => $request->bank_guarantee,
                        'letter_of_credit' => $request->letter_of_credit,
                        'deposit_general'  => $request->deposit_general,
                        'casa_individual'  => $request->casa_individual,
                        'td_individual'    => $request->td_individual,
                        'casa_corporate'   => $request->casa_corporate,
                        'td_corporate'     => $request->td_corporate,
                        'sagement_general' => $request->general_segment,
                        'sagement_bfs'     => $request->bfs,
                        'sagement_rfs'     => $request->rfs,
                        'sagement_pb'      => $request->pb,
                        'sagement_pcp'      => $request->pcp,
                        'sagement_afs'      => $request->afs,
                        'remarks'          => $request->remarks_product_segment,
                    ]);

                    /** update requester */
                    Requester::where('req_recid', $req_recid)->update(['subject' => $request->subject]);

                    /** if user want to remove attachement */
                    if ($request->att_remove) {
                        $att_delete = explode(',', $request->att_remove);
                        Documentupload::whereIn('id', $att_delete)->delete();
                    }
                    return $checker_mail;
                });
                // Procurement footer loop
                    $vender_name = $request->vendor_name;
                    $description = $request->vendor_description;
                    // delete item before replace
                    Procurementfooter::where('req_recid',$req_recid)->delete();
                    // end
                    if($vender_name !== null){
                        for ($i = 0; $i < count($vender_name); $i++) {
                           
                            $procurementfooter = new Procurementfooter();
                            $procurementfooter->req_recid = $req_recid;
                            $procurementfooter->vender_name = $vender_name[$i];
                            $procurementfooter->description = $description[$i];
                            $procurementfooter->save();
                        }
                    }
                    

                if ($request->hasFile('fileupload')) {
                    if (!file_exists(storage_path() . '/uploads/' . $req_recid)) {
                        File::makeDirectory(storage_path() . '/uploads/' . $req_recid, 0777, true);
                    }
                    $dt = Carbon::now();
                    $date_time = $dt->toDayDateTimeString();
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
                        $upload->doer_email = $request->req_email;
                        $upload->doer_name = $request->req_name;
                        $upload->activity_form = FormTypeEnum::ProcurementRequest();
                        $upload->activity_datetime = $date_time;
                        $upload->save();
                    }
                }

                $send_mail = new Sendemail();
                $content = 'Procurement Request has been submitted/approved.';
                $subject = 'Procurement Request';
                $return_email = $send_mail->sendEmailProcurementRequest(
                    $content,
                    $req_recid,
                    $request->req_name,
                    $request->req_department,
                    $request->req_position,
                    'Procurement Request',
                    $checker_mail,
                    $request->req_email,
                    $request->comment,
                    $request->subject,
                );
                if ($return_email == 'fail') {
                    Session::flash('success', 'Request submitted but email didnt send');
                } else {
                    Session::flash('success', 'Request submitted');
                }
                return redirect()->route('/');
            }

            DB::beginTransaction();
            $dt = Carbon::now();
            $date_time = $dt->toDayDateTimeString();

            //Procurement Table
            $req_email = Auth::user()->email;
            $req_date = $date_time;
            $within_budget_code = $request->withinbudget;
            $purpose = $request->purpose_rationale;
            $bid = $request->bid_waiver_sole;
            $justification = $request->justification_for_request;
            $vat = $request->vat;
            if($request->checkbox_vat == 1){
                $total_vat_final = "Y";
            }else{
                $total_vat_final = "N";
            }
            $procurement = new Procurement();
            $procurement->req_email = $req_email;
            $procurement->req_date = date('d/m/y');
            $procurement->purpose = $purpose;
            $procurement->bid = $bid;
            $procurement->justification = $justification;
            $procurement->vat          = $total_vat_final;
            $procurement->save();
            $last_id = $procurement->id;
            $req_id = Procurement::where('id', $last_id)->select('req_recid')->first();

            //Requester table
            $req_recid = $req_id->req_recid;
            $req_email = Auth::user()->email;
            $req_name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $req_branch = Auth::user()->department;
            $req_position = Auth::user()->position;
            $req_from = 'branch';
            $req_date = date('d/m/y');
            $due_expect_date = $request->expDate;
            $ref = $request->refNumber;
            $subject = $request->subject;
            $ccy = $request->currency;

            // procurementbody loop
            $description = $request->description;
            $br_dep_code = $request->br_dep_code;
            $budget_code = $request->budget_code;
            $alternativebudget_code = $request->alternative_budget_code;
            $unit = $request->unit;
            $qty = $request->qty;
            $unit_price = $request->unit_price;
            $total_estimate = $request->total_estimate;
            $delivery_date = $request->delivery_date;
            $within_budget = $request->within_budget;

            $budget_request = [];
            for ($i = 0; $i < count($budget_code); $i++) {
                array_push($budget_request, $budget_code[$i]);
            }
            if ($alternativebudget_code == '0') {
                for ($i = 0; $i < count($alternativebudget_code); $i++) {
                    array_push($budget_request, $alternativebudget_code[$i]);
                }
            }

            $buget_unique = DB::table('budgetdetail')
                ->where('budget_code', $budget_request[0])
                ->first();
            $condtion_save = 'Y';
            for ($i = 0; $i < count($budget_request); $i++) {
                $budget_thesame = DB::table('budgetdetail')
                    ->where('budget_code', $budget_request[$i])
                    ->where('budget_owner', $buget_unique->budget_owner)
                    ->first();
                if (empty($budget_thesame)) {
                    $condtion_save = 'N';

                    Session::flash('error', 'Different budget owner');
                    return redirect()->back();
                }
            }

            $total_2 = [];
            $sum = 0;

            for ($i = 0; $i < count($unit_price); $i++) {
                if ($ccy == 'KHR') {
                    $conversion = 4000;
                } else {
                    $conversion = 1;
                }
                $unitprice = $unit_price[$i] / $conversion;
                $total_1 = $qty[$i] * $unitprice;
                

                $budget = Budgetcode::where('budget_code', $budget_code[$i])->first();
                $alternative_budget = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();
                //* update column budget calculate */
                if($i == 0 or is_null($budget->budget_after_calculate_pr)){
                    $budget_after_calculate_pr_budget = $budget->temp;
                }else{
                    $budget_after_calculate_pr_budget = $budget->budget_after_calculate_pr;
                }
                if (!empty($alternative_budget)) {
                    $alternative_budget_remain = $alternative_budget->temp;
                } else {
                    $alternative_budget_remain = 0;
                }

                if ($budget_code[$i] == $alternativebudget_code[$i]) {
                    $alternative_budget = null;
                    $alternative_budget_remain = 0;
                }

                $budget_remain = $budget->temp;

                $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_1;
                if($request->checkbox_vat == 1){
                    $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_1*1.1;
                }

                if ($withinbudget_cond >= 0 and $budget_after_calculate_pr_budget >= $withinbudget_cond) {
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
                            $budget_al_spent = $total_1 - $budget_spent;
                        }
                    }
                } else {
                    $withinbudget = 'N';

                    if (empty($alternative_budget)) {
                        $current_budget_proc = Budgetcode::where('budget_code', $budget_code[$i])->first();
                        $budget_spent = $current_budget_proc->temp;
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
                $sum += $total_1;

                $unit_price_khr = $unitprice * 4000;
                $total_estimate_khr = $qty[$i] * $unit_price_khr;

                // check if checkbox vat is checked
                $request_vat = $request->vat;
                if($request->checkbox_vat == 1){
                    $total_vat =  $total_1*1.1;
                }else{
                    $total_vat =  $total_1;
                }
                // end
                $procurementbody = new Procurementbody();
                $procurementbody->req_recid = $req_recid;
                $procurementbody->description = $description[$i];
                $procurementbody->br_dep_code = $br_dep_code[$i];
                $procurementbody->budget_code = $budget_code[$i];
                $procurementbody->alternativebudget_code = $alternativebudget_code[$i];

                $procurementbody->unit = $unit[$i];
                $procurementbody->qty = $qty[$i];
                $procurementbody->unit_price = $unitprice;
                $procurementbody->total_estimate = $total_1;
                $procurementbody->budget_use = $budget_spent;
                $procurementbody->alternative_use = $budget_al_spent;
                $procurementbody->total = $total_1;

                $procurementbody->unit_price_khr = $unit_price_khr;
                $procurementbody->total_estimate_khr = $total_estimate_khr;
                $procurementbody->total_khr = $total_estimate_khr;

                $procurementbody->within_budget_code = $withinbudget;
                $procurementbody->vat = $request_vat;
                $procurementbody->save();
             
                $remainin = Budgetcode::where('budget_code', $budget_code[$i])->first();
                if ($total_vat > $remainin->temp) {
                    $procurement_budget = ['temp' => '0','budget_after_calculate_pr'=>$withinbudget_cond];
                } else {
                    $procurement_remaining = $remainin->temp - $total_vat;
                    $procurement_budget = ['temp' => $procurement_remaining,'budget_after_calculate_pr'=>$withinbudget_cond];
                }
               
                Budgetcode::where('budget_code', $budget_code[$i])->update($procurement_budget);

                $remainin_al = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();

                if (!empty($remainin_al)) {
                    if ($total_vat > $remainin_al->temp + $remainin->temp) {
                        $procurement_budget_al = ['temp' => '0','budget_after_calculate_pr'=>$withinbudget_cond];
                    } else {
                        $procurement_remaining_al = $remainin_al->temp - ($total_vat - $remainin->temp);
                        $procurement_budget_al = ['temp' => $procurement_remaining_al,'budget_after_calculate_pr'=>$withinbudget_cond];
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

            // Procurement footer loop
            $vender_name = $request->vendor_name;
            $description = $request->vendor_description;
            // delete item before replace
            Procurementfooter::where('id',$req_recid)->delete();
            // end
            for ($i = 0; $i < count($vender_name); $i++) {
                if($vender_name[$i] !== null){
                    $procurementfooter = new Procurementfooter();
                    $procurementfooter->req_recid = $req_recid;
                    $procurementfooter->vender_name = $vender_name[$i];
                    $procurementfooter->description = $description[$i];
                    $procurementfooter->save();
                }
                
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
            $sagement_pcp = $request->pcp;
            $sagement_afs = $request->afs;
            $sagement_pb = $request->pb;

            $procurementbottom = new Procurementbottom();

            $procurementbottom->req_recid = $req_recid;
            $procurementbottom->general = $general;
            $procurementbottom->loan_general = $loan_general;
            $procurementbottom->mortage = $mortage;
            $procurementbottom->busines = $busines;
            $procurementbottom->personal = $personal;
            $procurementbottom->card_general = $card_general;
            $procurementbottom->debit_card = $debit_card;
            $procurementbottom->credit_card = $credit_card;
            $procurementbottom->trade_general = $trade_general;
            $procurementbottom->bank_guarantee = $bank_guarantee;
            $procurementbottom->letter_of_credit = $letter_of_credit;
            $procurementbottom->deposit_general = $deposit_general;
            $procurementbottom->casa_individual = $casa_individual;
            $procurementbottom->td_individual = $td_individual;
            $procurementbottom->casa_corporate = $casa_corporate;
            $procurementbottom->td_corporate = $td_corporate;
            $procurementbottom->sagement_general = $sagement_general;
            $procurementbottom->sagement_bfs = $sagement_bfs;
            $procurementbottom->sagement_rfs = $sagement_rfs;
            $procurementbottom->sagement_pb = $sagement_pb;
            $procurementbottom->sagement_pcp = $sagement_pcp;
            $procurementbottom->sagement_afs = $sagement_afs;
            $procurementbottom->remarks = $request->remarks_product_segment;

            $procurementbottom->save();

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

            $requester = Defaultsave::defaultSave($req_recid, $req_email, $req_name, $req_branch, $req_position, '1', $due_expect_date, $ref, $subject, $ccy);
            for ($i = 0; $i < count($unit_price); $i++) {
                $budget_code_temp = Budgetcode::where('budget_code', $budget_code[$i])->first();
                $reset_temp = ['temp' => $budget_code_temp->remaining];
                Budgetcode::where('budget_code', $budget_code[$i])->update($reset_temp);
                if ($alternativebudget_code[$i] !== '0') {
                    $budget_code_temp = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();

                    $reset_temp = ['temp' => $budget_code_temp->remaining];
                    Budgetcode::where('budget_code', $alternativebudget_code[$i])->update($reset_temp);
                }
            }
            DB::commit();
            return Redirect::to('form/procurement/detail/' . Crypt::encrypt($req_recid . '___no'));
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function updateBid(Request $request){
        try {
            $bid = $request->bid_waiver_sole_update;
            $request_id = $request->request_id;
            Procurement::where('req_recid',$request_id)->update([
                'bid'=>$bid
            ]);
            return response()->json(['data'=>'success']);
        } catch (\Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function updateVendor(Request $request){
        try {
            $id = $request->id;
            $vendor_name_update = $request->vendor_name_update;
            $justification_update = $request->justification_update;
            $submit = $request->submit;
            if($submit == "update"){
                Procurementfooter::where('id',$id)->update([
                    'vender_name'=>$vendor_name_update,
                    'description'=>$justification_update
                ]);
            }elseif($submit == "delete"){
                Procurementfooter::where('id',$id)->delete();
            }else{
                $bid = new Procurementfooter();
                $bid->req_recid = $request->req_id;
                $bid->vender_name =$vendor_name_update;
                $bid->description =$justification_update;
                $bid->save();
            }
            return response()->json(['data'=>'success']);
        } catch (\Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return response()->json(['data'=>'success']);
        }
    }
    public function procurementDetail($req_recid)
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

            if (empty($requester_cond)) {
                Session::flash('error', 'No record found');
                return redirect()->route('form/procurement/new');
            }
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
            $userIsAccounting = Groupid::where('email', Auth::user()->email)
                                        ->where('group_id', 'GROUP_ACCOUNTING')
                                        ->where('groupid.status',1)
                                        ->first();
            $review_cond = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => Auth::user()->email])->first();
            $review_accounting ='';
            if($userIsAccounting){
                $review_accounting = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => 'accounting'])->first();
            }
            if (!empty($review_cond) or !empty($review_accounting)) {
                $review = '1';
            }
            $condition_view = Tasklist::where('req_recid', $req_recid)
                ->where('next_checker_group', Auth::user()->email)
                ->where('req_email', Auth::user()->email)
                ->whereNotNull('assign_back_by')
                ->first();

            $approve_cond = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => Auth::user()->email])->first();
            if (!empty($approve_cond)) {
                $approve = '1';
            }
            /**get budget detail */
            $total_all = Procurementbody::where('req_recid', $req_recid)->get();
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

            $procurement = Procurement::where('req_recid', $req_recid)->first();
            $top_mid = [
                'purpose' => $procurement->purpose,
                'bid' => $procurement->bid,
                'justification' => $procurement->justification,
                'comment_by_pr' => $procurement->comment_by_pr,
                'req_date' => $procurement->req_date,
                'vat'   => $procurement->vat,
            ];
            $body = Procurementbody::where('req_recid', $req_recid)->get();
            $budget_his = Budgethistory::where('req_recid', $req_recid)->orderBy('id', 'asc')->get()->toArray();
            $bottom = Procurementbottom::where('req_recid', $req_recid)->first();
            $footer = Procurementfooter::where('req_recid', $req_recid)->get();

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
                ->select('auditlog.id','auditlog.doer_role','auditlog.doer_action','auditlog.step_action','auditlog.doer_name AS name','auditlog.doer_email AS email', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime','activitydescription.activity_code')
                ->get();
            $checker = Groupid::where('email', $requester_cond->req_email)->where('role_id', '!=', 4)->first();
            if (empty($checker)) {
                Session::flash('error', 'No reviewer or approver found');
                return redirect()->back();
            }
            $ceos = GroupId::where('group_id','GROUP_CEO')->where('status',1)->first();
            $ceo_email = $ceos->email.'/';
            $dceos = GroupId::select('groupid.email','groupid.role_id','usermgt.fullname')
                        ->join('usermgt', 'groupid.email', 'usermgt.email')
                        ->where('groupid.group_id','GROUP_DCEO')->where('groupid.status',1)->first();
            $dceo_email = $dceos->email.'/';
            $cdo = GroupId::where('group_id','GROUP_CDO')->where('status',1)->first();
            $dceo_office = GroupId::select('groupid.email','groupid.role_id','usermgt.fullname')
                        ->join('usermgt', 'groupid.email', 'usermgt.email')
                        ->where('groupid.group_id','GROUP_DCEO_OFFICE')->where('groupid.status',1)->first();
            $dceo_email_office = $dceo_office->email.'/'.$dceo_office->role;
            // find email under cdo/dceo
            $email_under = new GroupId();
            $email_under_cdo = $email_under->findEmailUnderCDO();
            $email_under_dceo = $email_under->findEmailUnderDCEO();

            $requester_progress = Requester::where('req_recid', $req_recid)->first();

            $review_progress = Reviewapprove::where('reviewapprove.req_recid', $req_recid)->first();

            $review1_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.second_review')->where('reviewapprove.req_recid', $req_recid)->first();

            $budgetowner_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.budget_owner')->where('reviewapprove.req_recid', $req_recid)->first();

            $approve_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.approve')->where('reviewapprove.req_recid', $req_recid)->first();
            $final_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.final')->where('reviewapprove.req_recid', $req_recid)->first();

            $pending_at = Tasklist::join('users', 'users.email', 'tasklist.next_checker_group')->where('tasklist.req_recid', $req_recid)->first();
            if($pending_at){
                $pending_at = $pending_at->firstname .' ' . $pending_at->lastname;
            }
            if ($task_listing->next_checker_group == 'accounting') {
                $pending_at = 'Accounting & Finance';
            }
            $insuficient = Tasklist::where('tasklist.req_recid', $req_recid)->where('insufficient', 'Y')->first();

            $group_final = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->whereIn('groupid.group_id', ['GROUP_ACD','GROUP_INFRA','GROUP_MARKETING', 'GROUP_PROCUREMENT','GROUP_LEARNING_PEOPLE','GROUP_ADMINISTRATION'])->where('groupid.status',1)->get();
            if ($requester == '1' or !empty($condition_view)) {
                // multi reviewer
                $group_multiReviewer = Groupid::join('usermgt', 'groupid.email', 'usermgt.email')
                    ->where('groupid.email', '!=', Auth::user()->email)
                    ->whereIn('groupid.role_id', ['2','3'])
                    ->where('groupid.status',1)
                    ->get(); 
                $group_requester = DB::table('groupid')
                    ->join('usermgt', 'groupid.email', 'usermgt.email')
                    ->where('groupid.group_id', $checker->group_id)
                    ->where('groupid.email', '!=', Auth::user()->email)
                    ->where('groupid.role_id', '!=', '1')
                    ->where('groupid.group_id', '!=', 'DCEO_OFFICE')
                    ->where('groupid.status', '=', '1')
                    ->get();
                if (empty($group_requester)) {
                    Session::flash('error', 'No reviewer or approver found');
                    return redirect()->back();
                }
                $total_spent_arr = Procurementbody::where('req_recid', $req_recid)->get();
                $total_spent = 0;
                foreach ($total_spent_arr as $key => $value) {
                    $total_spent += $value->total_estimate;
                }
                if($procurement->vat == 'Y'){
                    $total_spent = $total_spent*1.1;
                }else{
                    $total_spent = $total_spent;
                }
                $group_approver_co =[];
                if($procurement->bid === "no")
                    if($total_spent <= 3000){
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_MEMBER_EXCO','GROUP_CDO','GROUP_CEO','GROUP_SECONDLINE_EXCO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->where('groupid.status',1)
                                            ->orderBy('usermgt.fullname')->get()
                                            ->unique('email');
                    }elseif($total_spent <=5000){
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CDO','GROUP_CEO','GROUP_MEMBER_EXCO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->where('groupid.status',1)
                                            ->orderBy('usermgt.fullname')->get()->unique('email');
                    }elseif($total_spent <= 15000){
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CDO','GROUP_CEO'])
                                            ->where('groupid.status',1)
                                            ->orderBy('usermgt.fullname')->get()
                                            ->unique('email');
                    }else{
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CEO'])
                                            ->orderBy('usermgt.fullname')->get()->unique('email');
                    }
                else{
                    if ($total_spent <= 1500) {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CEO','GROUP_MEMBER_EXCO','GROUP_CDO','GROUP_SECONDLINE_EXCO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()
                                            ->unique('email');
                        $group_approver_co = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_SECONDLINE_EXCO','GROUP_CDO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->whereNotIn('groupid.email',[$ceos->email,$dceos->email])
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()->unique('email'); 
                    }elseif($total_spent <= 2500 ){
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CEO','GROUP_CDO','GROUP_MEMBER_EXCO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()
                                            ->unique('email');
                        $group_approver_co = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_SECONDLINE_EXCO','GROUP_CDO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->whereNotIn('groupid.email',$email_under_dceo)
                                            ->whereNotIn('groupid.email',[$ceos->email,$dceos->email])
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()->unique('email');  
                    }elseif($total_spent <= 7500){
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CEO','GROUP_CDO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()
                                            ->unique('email');
                        $group_approver_co = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_MEMBER_EXCO','GROUP_CDO'])
                                            ->whereNotIn('groupid.email',$email_under_cdo)
                                            ->whereNotIn('groupid.email',[$ceos->email,$dceos->email])
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()->unique('email');   
                    }else {
                        $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                                            ->whereIn('groupid.group_id',['GROUP_CEO'])
                                            ->where('groupid.status',1)
                                            ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                                            ->orderBy('usermgt.fullname')
                                            ->get()->unique('email');
                    }
                }

                $condition_manyreview = Procurementbody::where('req_recid', $req_recid)->where('within_budget_code', 'N')->first();

                $many_review = 'N';

                if (empty($condition_manyreview)) {
                    $many_review = 'Y';
                }

                $ceoOffice = '';
                $ceo = '';
                $cfo = '';
                $budgetOwner = '';
                if ($many_review == 'Y') {
                    $procurementBody = collect($body)->first();
                    $budgetOwner = Budgetcode::join('users', 'users.email', '=', 'budgetdetail.budget_owner')
                        ->where('budgetdetail.budget_code', $procurementBody['budget_code'])
                        ->first();
                }

                if ($total_spent > 5000 && $many_review == 'Y') {
                    $ceoOffice = Groupid::where('groupid.group_id', 'GROUP_MDOFFICE')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                    $ceo       = Groupid::where('groupid.group_id', 'GROUP_CEO')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                }
                if ($total_spent > 2500 && $procurement->bid == 'yes') {
                    $ceoOffice = Groupid::where('groupid.group_id', 'GROUP_MDOFFICE')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                    $ceo       = Groupid::where('groupid.group_id', 'GROUP_CEO')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                }
                if ($many_review == 'N') {
                    $ceoOffice = Groupid::where('groupid.group_id', 'GROUP_MDOFFICE')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                    $ceo       = Groupid::where('groupid.group_id', 'GROUP_CEO')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                    $cfo       = Groupid::where('groupid.group_id', 'GROUP_CFO')->where('groupid.is_cfo','1')->join('usermgt', 'groupid.email', 'usermgt.email')->where('groupid.status',1)->first();
                }

                return view('approver.procurement', compact(
                    'budget_his',
                    'top',
                    'top_mid',
                    'body',
                    'bottom',
                    'footer',
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
                    'group_approver_co',
                    'group_final',
                    'query',
                    'total_spent',
                    'many_review',
                    'param_url_response',
                    'budgetOwner',
                    'ceo_email',
                    'ceoOffice',
                    'ceo',
                    'cfo',
                    'group_multiReviewer',
                    'review_progress',
                    'dceo_email',
                    'dceo_email_office',
                    'dceo_office',
                    'dceos',
                    'totalAndYTD',
                    'budgetcode_na'
                ));
            } else {
                $tasklist = Tasklist::where('req_recid', $req_recid)->first();
                $owner = $tasklist->req_email;
                $within_budget = $tasklist->within_budget;
                $step_number = $tasklist->step_number;

                $final_res = 'N';
                $final = Reviewapprove::where('final', Auth::user()->email)->first();
                $final_checker = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('step_number', '>', 6)->first();

                $last_step = $tasklist->step_number;
                if (!empty($final) and !empty($final_checker)) {
                    $final_res = 'Y';
                }
                $approvers = $tasklist->getAllApprovers();
                return view('approver.procurementapprove', compact(
                    'insuficient',
                    'request_status',
                    'top',
                    'top_mid',
                    'body',
                    'bottom',
                    'footer',
                    'document',
                    'description_response',
                    'auditlog',
                    'requester',
                    'review',
                    'approve',
                    'final_res',
                    'query',
                    'total_spent',
                    'param_url_response',
                    'pending_at',
                    'group_final',
                    'requester_progress',
                    'alternative_budget_codes',
                    'approvers',
                    'review_progress',
                    'totalAndYTD',
                    'budgetcode_na'
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
        try {
            $grand_total = $request->grand_total;
            /** find procurment request */
            $procurement = Procurement::firstWhere('req_recid', $request->req_recid);
            $requester = Requester::firstWhere('req_recid', $request->req_recid);
            if (!$procurement) {
                Session::flash('Not found', "Procurement {$request->req_recid} not found.");
                return redirect()->back();
            }

            /**@var User $user */
            $user = Auth::user();

            /** make sure current user is current approver */
            if (!$user->isCurrentApproverForProcurment($procurement) && $request->submit != 'query') {
                Session::flash('Not allow', "This request is not belong to you any more.");
                return redirect()->back();
            }
            $message = DB::transaction(function () use ($request, $user, $procurement,$grand_total) {
                if ($request->submit == 'approve') {
                    $request_no = Reviewapprove::where('req_recid',$request->pr_ref_no)->first();
                    $user->approverProcurement($procurement, $request->comment);
                    if($request_no->final == $user->email){
                        return 'Procurement Request has been approved successfully';
                    }
                    return 'New Procurement Request for your review/approval';
                }

                if ($request->submit == 'back') {
                    $user->assignProcurmentBack($procurement, $request->comment,$grand_total);
                    return 'Procurement Request has been assigned back successfully';
                }

                if ($request->submit == 'query') {
                    $user->queryProcurement($procurement, $request->comment,$user->email);
                    return 'Procurement Request has been queried successfully';
                }

                if ($request->submit == 'backtocfo') {
                    $user->assignProcurementBackToCFO($procurement, $request->comment);
                    return 'Procurement Request has been assigned to CFO successfully';
                }

                if ($request->submit == 'transfer') {
                    $user->transferProcurement($procurement, $request->comment, $request->transfer_to);
                    return 'Procurement Request has been transfer successfully';
                }

                if ($request->submit == 'reject') {
                    $user->rejectProcurment($procurement, $request->comment);
                    return 'Procurement Request has been rejected successfully';
                }

                return '';
            });

            if (!$message) {
                Session::flash('error', 'Please Contact Admin');
                return redirect()->back();
            }

            /** send email */
            $taskList = Tasklist::firstWhere('req_recid', $request->req_recid);
            $send_mail = new Sendemail();
            $return_email = $send_mail->sendEmailProcurementRequest(
                $content       = $message,
                $req_recid     = $request->req_recid,
                $req_name      = $taskList->req_name,
                $req_branch    = $taskList->req_branch,
                $req_position  = $taskList->req_position,
                $subject       = 'Procurement Request',
                $checker_email = $taskList->next_checker_group,
                $req_email     = $taskList->req_email,
                $comment       = $request->comment,
                $request_subject = $requester->subject
            );
            if ($return_email == 'fail') {
                Session::flash('success', "{$message} but email did not send");
            } else {
                Session::flash('success', $message);
            }
            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function updateCheckBox(Request $request){
        try {
            $procurementbody = Procurementbody::where('req_recid',$request->request_id)->get();
            $procurementbody_request = Procurementbody::where('req_recid',$request->request_id)->first();
            $vat = $procurementbody_request->vat;
            $first_budget_codes = [];
            foreach($procurementbody as $key=>$value){
                $code = 0;
                if(in_array($value->budget_code, $first_budget_codes))
                {
                    $code = 1;
                }
                $budget_code = Budgetcode::where('budget_code',$value->budget_code)->first();
                if( $key == 0 or is_null($budget_code->budget_after_calculate_pr) or $code == 0){
                    $budget_after_calculate_pr_budget = $budget_code->temp;
                }else{
                    $budget_after_calculate_pr_budget = $budget_code->budget_after_calculate_pr;
                }
                
                $total_estimate = $value->total_estimate;
                $within_budget_code =  $budget_after_calculate_pr_budget - $total_estimate;
                if($request->check_box == 1){
                    $within_budget_code =  $budget_after_calculate_pr_budget - $total_estimate*1.1;
                }
                
                if($within_budget_code >= 0){
                    $withinbudget = 'Y';
                }else{
                    $withinbudget = 'N';
                }
                //** update budget code calculate field */

                if($request->check_box == 0){
                    Procurementbody::where('req_recid',$request->request_id)->where('id',$value->id)->update([
                        'vat' => 0,
                        'within_budget_code' => $withinbudget,
                    ]);
                    Procurement::where('req_recid',$request->request_id)->update([
                        'vat' => 'N',
                    ]);
                    $total_vat =0;
                }else{
                    Procurementbody::where('req_recid',$request->request_id)->where('id',$value->id)->update([
                        'within_budget_code' => $withinbudget,
                    ]);
                    Procurement::where('req_recid',$request->request_id)->update([
                        'vat' => 'Y',
                    ]);
                }
                Budgetcode::where('budget_code', $value->budget_code)->update(['budget_after_calculate_pr' => $within_budget_code]);
                array_push($first_budget_codes,$value->budget_code);
                
            }
            if($request->check_box == 1){
                $sum =0;
                foreach($procurementbody as $value){
                    $sum += $value->total_estimate;
                }
                $total_vat =number_format((float)($sum*0.1), 2, '.', '');
                Procurementbody::where('req_recid',$request->request_id)->update([
                    'vat' => $total_vat
                ]);
            }
           
            
            return response()->json(['data'=>'success']);
        } catch (\Exception $e) {
            Log::info($e);
            Session::flash('error', 'Please Contact Admin');
            return response()->json(['data'=>'success']);
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
            $br_dep_code = $request->branchcode;
            $budget_code = $request->budget_code;
            $alternativebudget_code = $request->alternativebudget_code;
            $unit = $request->unit;
            $qty = $request->qty;
            $unit_price = $request->unit_price;
            $total_estimate = $request->total_estimate;
            $delivery_date = $request->delivery_date;
            $total = $request->total;
            $within_budget_code = $request->within_budget_code;
            $vat_item = $request->vat_dis;
            $vat = Procurementbody::where('req_recid', $req_recid)->first();
            $vat_payment = Procurement::where('req_recid', $req_recid)->first();
            $status_vat = $vat_payment->vat;

            //**update withinbudget code */
            if($id and $condition == 'update'){
                Procurementbody::where('id',$id)->update([
                    'budget_code'       => $budget_code,
                    'total_estimate'    => $total_estimate,
                ]);
            }
            $calculate_budgetcode = $this->calculateBudgetCode($req_recid,$status_vat);
           
            //**end */
            // check if update item
            $update_amount = Procurementbody::where('id', $id)->first();
            if($update_amount){
                $amount_item = $update_amount->total;
                $old_vat_amount = $update_amount->vat;
                $old_vat_item = (float)$amount_item*0.1;
                $new_vat_item = (float)$total_estimate*0.1;
                $vat_final_old = $old_vat_amount - $old_vat_item;
                $new_vat_amount =  $vat_final_old +  $new_vat_item;
            }
            if(!$vat_item){
                $vat_item = (float)$vat->vat; 
            }else{
                if(!$id){
                    $vat_item = (float)$vat_item + (float)$vat->vat; 
                }else{
                    $vat_item = $new_vat_amount;
                }
                
            }
            if($status_vat == "N"){
                $vat_item = 0;
            }
            $ccy_cond = Requester::where('req_recid', $req_recid)->first();
            $ccy = $ccy_cond->ccy;
            if ($ccy == 'KHR') {
                $conversion = 4000;
            } else {
                $conversion = 1;
            }
            $total_2 = [];
            $sum = [];
            $unitprice = $unit_price / $conversion;
            $total_1 = $qty * $unitprice;

            if($vat_payment->vat == 'Y'){
                $total_vat = $total_1*1.1;
            }else{
                $total_vat = $total_1;
            }
            if ($conversion = 4000) {
                $total_khr = $qty * $unit_price;
            }
            
            $budget = Budgetcode::where('budget_code', $budget_code)->first();
            $alternative_budget = Budgetcode::where('budget_code', $alternativebudget_code)->first();
            if (!empty($alternative_budget)) {
                $alternative_budget_remain = $alternative_budget->budget_after_calculate_pr;
            } else {
                $alternative_budget_remain = 0;
            }
            if ($budget_code == $alternativebudget_code) {
                $alternative_budget = null;
                $alternative_budget_remain = 0;
            }

            $budget_remain = $budget->budget_after_calculate_pr;
            $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_vat;
            if($status_vat == "N"){
                $vat_item = 0;
                $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_vat*1.1;
            }

            if(!$update_amount){
                if ($withinbudget_cond >= 0 ) {
                    $withinbudget = 'Y';
                    if (empty($alternativebudget_code)) {
                        $budget_spent = $total_vat;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_vat) {
                            $budget_spent = $total_vat;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;
                            $budget_al_spent = $total_vat - $budget_spent;
                        }
                    }
                } else {
                    $withinbudget = 'N';
                    if (empty($alternativebudget_code)) {
                        $current_budget_proc = Budgetcode::where('budget_code', $budget_code)->first();
                        $budget_spent = $current_budget_proc->temp;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_vat) {
                            $budget_spent = $budget_remain - $total_vat;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;
    
                            $budget_al_spent = $alternative_budget_remain;
                        }
                    }
                }
            }else{
                if ($update_amount->within_budget_code >= 'Y' ) {
                    $withinbudget = 'Y';
                    if (empty($alternativebudget_code)) {
                        $budget_spent = $total_vat;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_vat) {
                            $budget_spent = $total_vat;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;
                            $budget_al_spent = $total_vat - $budget_spent;
                        }
                    }
                } else {
                    $withinbudget = 'N';
                    if (empty($alternativebudget_code)) {
                        $current_budget_proc = Budgetcode::where('budget_code', $budget_code)->first();
                        $budget_spent = $current_budget_proc->temp;
                        $budget_al_spent = 0;
                    } else {
                        if ($budget_remain > $total_vat) {
                            $budget_spent = $budget_remain - $total_vat;
                            $budget_al_spent = 0;
                        } else {
                            $budget_spent = $budget_remain;
    
                            $budget_al_spent = $alternative_budget_remain;
                        }
                    }
                }
            }

            $total_estimate_from_db = 0;
            $procurementbody = Procurementbody::where('req_recid', $req_recid)->where('id', '!=', $id)->get();
            if (!empty($procurementbody)) {
                foreach ($procurementbody as $key => $value) {
                    $total_estimate_from_db += $value->total_estimate;
                }
            }

            if ($budget_code == $alternativebudget_code) {
                $budget_al_spent = 0;
                $alternativebudget_code = 0;
            }
            $budget_his_id = $request->budget_his_id;
            $status_vat = $vat_payment->vat;
            if ($condition == 'update') {
                $unit_price_khr = $unitprice * 4000;
                $total_estimate_khr = $qty * $unit_price_khr;

                $result = Procurementbody::firstOrNew(['id' => $id]);
                $result->req_recid = $req_recid;
                $result->description = $description;
                $result->br_dep_code = $br_dep_code;
                $result->budget_code = $budget_code;
                $result->alternativebudget_code = $alternativebudget_code;
                $result->unit = $unit;
                $result->qty = $qty;
                $result->vat =$vat_item;
                $result->unit_price = $unitprice;
                $result->total_estimate = $total_1;
                $result->delivery_date = $delivery_date;
                $result->budget_use = $budget_spent;
                $result->alternative_use = $budget_al_spent;
                $result->total = $total_1;

                $result->unit_price_khr = $unit_price_khr;
                $result->total_estimate_khr = $total_estimate_khr;
                $result->total_khr = $total_estimate_khr;
                $result->within_budget_code = $withinbudget;
                $result->save();

                $budget_hsitory = Budgethistory::firstOrNew(['id' => $budget_his_id]);

                $budget_hsitory->req_recid = $req_recid;
                $budget_hsitory->budget_code = $budget_code;
                $budget_hsitory->alternative_budget_code = $alternativebudget_code;
                $budget_hsitory->budget_amount_use = $budget_spent;
                $budget_hsitory->alternative_amount_use = $budget_al_spent;
                $budget_hsitory->save();
                $calculate_budgetcode = $this->calculateBudgetCode($req_recid,$status_vat);
                Procurementbody::where('req_recid', $req_recid)->update([
                    'vat' => $vat_item
                ]);
                
                DB::commit();
                Session::flash('success', 'Item updated');
                return redirect()->back();
            } else {
                $result = Procurementbody::find($id);
                $result1 = Budgethistory::find($budget_his_id);
                $result->delete();
                $result1->delete();
                //update vat after delete
                $procurement_request =  Procurementbody::where('req_recid', $req_recid)->get();

                $sum = 0;
                foreach( $procurement_request as $item){
                    $sum =  (float)$sum + (float)$item->total;
                }
                $vat_after_delete = (float)$sum * 0.1;
                if($status_vat == 'N'){
                    $vat_after_delete = (float)$sum;
                }
                Procurementbody::where('req_recid', $req_recid)->update([
                    'vat' => $vat_after_delete
                ]);
                //end
                //**update vat all item */
                $calculate_budgetcode = $this->calculateBudgetCode($req_recid,$status_vat);
                DB::commit();
                Session::flash('success', 'Item updated');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function generatePDF($req_recid)
    {
        // try {
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

        if (empty($requester_cond)) {
            Session::flash('error', 'No record found');
            return redirect()->route('form/procurement/new');
        }
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

        $approve_cond = Tasklist::where(['req_recid' => $req_recid, 'next_checker_group' => Auth::user()->email])->first();
        if (!empty($approve_cond)) {
            $approve = '1';
        }

        $total_all = Procurementbody::where('req_recid', $req_recid)->get();
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

        $procurement = Procurement::where('req_recid', $req_recid)->first();
        $top_mid = [
            'purpose' => $procurement->purpose,
            'bid' => $procurement->bid,
            'justification' => $procurement->justification,
            'comment_by_pr' => $procurement->comment_by_pr,
            'req_date' => $procurement->req_date,
        ];

        $body = Procurementbody::where('req_recid', $req_recid)->get();
        $budget_his = Budgethistory::where('req_recid', $req_recid)->orderBy('id', 'asc')->first();

        $bottom = Procurementbottom::where('req_recid', $req_recid)->first();
        $footer = Procurementfooter::where('req_recid', $req_recid)->get();

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
            ->select('auditlog.activity_code','auditlog.step_action','auditlog.doer_email as email','auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.created_at AS datetime')
            ->get();

        $checker = Groupid::where('email', $requester_cond->req_email)->where('role_id', '!=', 4)->first();
        if (empty($checker)) {
            Session::flash('error', 'No reviewer or approver found');
            return redirect()->back();
        }

        $requester_progress = Requester::where('req_recid', $req_recid)->first();

        $review_progress = Reviewapprove::where('reviewapprove.req_recid', $req_recid)->first();

        $review1_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.second_review')->where('reviewapprove.req_recid', $req_recid)->first();

        $budgetowner_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.budget_owner')->where('reviewapprove.req_recid', $req_recid)->first();
        $approve_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.approve')->where('reviewapprove.req_recid', $req_recid)->first();
        $final_progress = Reviewapprove::join('users', 'users.email', 'reviewapprove.final')->where('reviewapprove.req_recid', $req_recid)->first();

        $pending_at = Tasklist::join('users', 'users.email', 'tasklist.next_checker_group')->where('tasklist.req_recid', $req_recid)->first();
        $insuficient = Tasklist::where('tasklist.req_recid', $req_recid)->where('insufficient', 'Y')->first();

        $group_final = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->whereIn('groupid.group_id', ['GROUP_ACD','GROUP_INFRA','GROUP_PROCUREMENT', 'GROUP_ADMINISTRATION', 'GROUP_MARKETING', 'GROUP_LEARNING_PEOPLE'])->get();
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

            $total_spent_arr = Procurementbody::where('req_recid', $req_recid)->get();
            $total_spent = 0;
            foreach ($total_spent_arr as $key => $value) {
                $total_spent += $value->total_estimate;
            }

            if ($total_spent > 3000) {
                $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.budget', 5000)
                    ->select('usermgt.firstname', 'usermgt.lastname', 'groupid.email')
                    ->orderBy('usermgt.fullname')
                    ->get()->unique('email');
            } else {
                $group_approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')->where('groupid.budget', '>=', 3000)->where('groupid.status',1)->orderBy('usermgt.fullname')->get()->unique('email');
            }

            $condition_manyreview = Procurementbody::where('req_recid', $req_recid)->where('within_budget_code', 'N')->first();

            $many_review = 'N';

            if (empty($condition_manyreview)) {
                $many_review = 'Y';
            }
            return view('approver.procurement', compact(
                                                    'budget_his',
                                                    'top',
                                                    'top_mid', 
                                                    'body', 
                                                    'bottom', 
                                                    'footer', 
                                                    'document', 
                                                    'description_response', 
                                                    'auditlog', 'group_requester', 
                                                    'requester', 'condition_view', 
                                                    'review', 
                                                    'approve', 
                                                    'request_status', 
                                                    'budget_code', 
                                                    'alternative_budget_codes', 
                                                    'dep_code', 'group_approver', 
                                                    'group_final', 
                                                    'query', 
                                                    'total_spent', 
                                                    'many_review', 
                                                    'param_url_response',
                                                    'totalAndYTD',
                                                    'budgetcode_na'
                                                ));
        } else {
            $tasklist = Tasklist::where('req_recid', $req_recid)->first();
            $owner = $tasklist->req_email;
            $within_budget = $tasklist->within_budget;
            $step_number = $tasklist->step_number;

            $final_res = 'N';
            $final = Reviewapprove::where('final', Auth::user()->email)->first();
            $final_checker = Tasklist::where('req_recid', $req_recid)->where('next_checker_group', Auth::user()->email)->where('step_number', '>', 3)->first();

            $last_step = $tasklist->step_number;
            if (!empty($final) and !empty($final_checker)) {
                $final_res = 'Y';
            }
            $dceo_office = Groupid::where('group_id','GROUP_DCEO_OFFICE')->where('status',1)->first();
            $ceo_office = Groupid::where('group_id','GROUP_MDOFFICE')->where('status',1)->first();
            $pdf = PDF::loadView('approver.procurementpdfform', compact('ceo_office',
                                                                    'dceo_office',
                                                                    'insuficient', 
                                                                    'request_status', 
                                                                    'top', 
                                                                    'top_mid', 
                                                                    'body', 
                                                                    'bottom', 
                                                                    'footer', 
                                                                    'document', 
                                                                    'description_response', 
                                                                    'auditlog', 
                                                                    'requester', 
                                                                    'review', 
                                                                    'approve', 
                                                                    'final_res', 
                                                                    'query', 
                                                                    'total_spent', 
                                                                    'param_url_response', 
                                                                    'review_progress', 
                                                                    'review1_progress', 
                                                                    'budgetowner_progress', 
                                                                    'approve_progress', 
                                                                    'pending_at', 
                                                                    'group_final', 
                                                                    'requester_progress', 
                                                                    'final_progress', 
                                                                    'alternative_budget_codes',
                                                                    'totalAndYTD',
                                                                    'budgetcode_na'
                                                                ));
            return $pdf->download($req_recid . '.pdf');
        }
    }

    private function transformApprover($approver)
    {
        if (!$approver) {
            return (object)(['email' => null, 'role' => null,]);
        }

        $approvers = explode('/', $approver);
        return (object)(['email' => $approvers[0], 'role' => $approvers[1],]);
    }

    private function findBudgetOwnerForProcurement($reqRecid)
    {
        /** find budget owner */
        $procurementBody = Procurementbody::firstWhere('req_recid', $reqRecid);
        $budgetDetail    = BudgetDetail::firstWhere('budget_code', $procurementBody->budget_code);
        return  $budgetDetail;
    }

    public function downloadExcel()
    {
        $path = public_path('/static/template/procurement-template.xlsx');
        return response()->download($path);
    }
    public function calculateBudgetCode($requestId, $statusVat){
        $procurementbody = Procurementbody::where('req_recid', $requestId)->get();
        $procurementbody_request = Procurementbody::where('req_recid', $requestId)->first();
        $first_budget_codes = [];
        foreach($procurementbody as $key=>$value){
            $code = 0;
            if(in_array($value->budget_code, $first_budget_codes))
            {
                $code = 1;
            }
            $budget_code_before_update = Budgetcode::where('budget_code',$value->budget_code)->first();
            if( $key == 0 or is_null($budget_code_before_update->budget_after_calculate_pr) or $code == 0){
                $budget_after_calculate_pr_budget = $budget_code_before_update->temp;
            }else{
                $budget_after_calculate_pr_budget = $budget_code_before_update->budget_after_calculate_pr;
            }
            
            $total_estimate = $value->total_estimate;
            $within_budget_code =  $budget_after_calculate_pr_budget - $total_estimate;
            if($statusVat == 'Y'){
                $within_budget_code =  $budget_after_calculate_pr_budget - $total_estimate*1.1;
            }
            if($within_budget_code >= 0){
                $withinbudget = 'Y';
            }else{
                $withinbudget = 'N';
            }
            //** update budget code calculate field */
            if($statusVat == 'N'){
                Procurementbody::where('req_recid',$requestId)->where('id',$value->id)->update([
                    'vat' => 0,
                    'within_budget_code' => $withinbudget,
                ]);
                Procurement::where('req_recid',$requestId)->update([
                    'vat' => 'N',
                ]);
                $total_vat =0;
            }else{
                Procurementbody::where('req_recid',$requestId)->where('id',$value->id)->update([
                    'within_budget_code' => $withinbudget,
                ]);
                Procurement::where('req_recid',$requestId)->update([
                    'vat' => 'Y',
                ]);
            }
            Budgetcode::where('budget_code', $value->budget_code)->update(['budget_after_calculate_pr' => $within_budget_code]);
            array_push($first_budget_codes,$value->budget_code);
        }
        return $procurementbody;
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
