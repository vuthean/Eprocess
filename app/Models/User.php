<?php

namespace App\Models;

use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Http\Middleware\TrustHosts;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Myclass\Sendemail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'userid',
        'division',
        'department',
        'position',
        'mobile',
        'status',
        'groupid',
        'lastlogin',
        'password',
        'fullname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function groupIds()
    {
        return $this->hasMany(Groupid::class, 'email', 'email');
    }

    public function isGroupAccounting()
    {
        return Groupid::where('login_id', $this->userid)->where('group_id', 'GROUP_ACCOUNTING')->first();
    }

    public function isAllowToAccessProcurementRecord()
    {
        $group = Groupid::whereIn('group_id', ['GROUP_ACD','GROUP_INFRA','GROUP_PROCUREMENT', 'GROUP_ADMINISTRATION', 'GROUP_MARKETING', 'GROUP_LEARNING_PEOPLE'])->where('status',1)->select('email')->get()->toArray();
        $procurement = $this->mergeMultiArray($group);
        return in_array($this->email, $procurement);
    }

    public function isAllowToAccessAdvanceRecord()
    {
        /** based on docurement, we allow only group Finance, Accounting and CFO */
        $currentUser = Auth::user();

        $groupId = Groupid::whereIn('group_id', ['GROUP_CFO', 'GROUP_FINANCE', 'GROUP_ACCOUNTING'])
            ->where('email', $currentUser->email)
            ->where('status',1)
            ->first();
        if ($groupId) {
            return true;
        }
        return false;
    }

    public function getPaymentRecordAlloanceEmails()
    {

        /** Allow all staff’s users in Finance, Accountant and CFO to view payment record */
        $allowGroups = ['GROUP_FINANCE', 'GROUP_ACCOUNTING', 'GROUP_CFO'];
        $groupUsers  = Groupid::whereIn('group_id', $allowGroups)
            ->where('status',1)
            ->select('email')
            ->get();

        $emails = collect($groupUsers)->pluck('email');

        return $emails;
    }

    public function isAllowToViewPaymentRecord()
    {
        $allowanceEmails = $this->getPaymentRecordAlloanceEmails();
        $emails = collect($allowanceEmails)->toArray();

        return in_array($this->email, $emails);
    }

    public function getPaymentRecord()
    {
        $payments = Tasklist::join('formname', 'tasklist.req_type', 'formname.id')
            ->join('reviewapprove', 'reviewapprove.req_recid', 'tasklist.req_recid')
            ->join('users', 'users.email', '=', 'tasklist.req_email')
            ->join('requester', 'requester.req_recid', '=', 'tasklist.req_recid')

            ->select('tasklist.req_recid', 'users.fullname as requester_name', 'users.department as from_department', 'formname.description as description', 'requester.ccy')
            ->selectRaw('date_format(tasklist.created_at, "%d-%m-%Y %h:%i") req_date')
            ->selectRaw('(select date_format(a.created_at, "%Y-%m-%d") created_at from auditlog a where a.req_recid = tasklist.req_recid order by a.created_at desc limit 1) as for_searching_date')
            ->selectRaw('(select date_format(a.created_at, "%d-%m-%Y %h:%i") created_at from auditlog a where a.req_recid = tasklist.req_recid order by a.created_at desc limit 1) as approved_at')

            ->selectRaw('(select sum(p.total_khr) from paymentbody p where p.req_recid = tasklist.req_recid ) as total_khr')
            ->selectRaw('(select discount_khr from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as discount_khr')
            ->selectRaw('(select vat_khr from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as vat_khr')
            ->selectRaw('(select wht_khr from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as wht_khr')
            ->selectRaw('(select deposit_khr from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as deposit_khr')

            ->selectRaw('(select sum(p.total) from paymentbody p where p.req_recid = tasklist.req_recid) as total_usd')
            ->selectRaw('(select discount from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as discount_usd')
            ->selectRaw('(select vat from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as vat_usd')
            ->selectRaw('(select wht from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as wht_usd')
            ->selectRaw('(select deposit from paymentbody p where p.req_recid = tasklist.req_recid limit 1) as deposit_usd')

            ->selectRaw('(select (case when r.ccy = "USD" then "1" else 4000 end) as exchange_rate from requester r where r.req_recid = tasklist.req_recid limit 1) as exchange_rate')

            ->where('tasklist.req_type', 2)
            ->where('tasklist.req_status', '<>', '005')
            ->where('tasklist.next_checker_group', 'accounting')
            ->where('tasklist.step_number', '>', '3')
            ->get();

        /**transdform calculate grand total */
        $tranformedPayments = collect($payments)->each(function ($payment) {
            $transformedPayment = $payment;

            /**calculate khr grand total */
            $transformedPayment['grand_total_khr'] = $payment->total_khr - $payment->discount_khr + $payment->vat_khr -  $payment->wht_khr - $payment->deposit_khr;

            /**calculate usd grand total */
            $transformedPayment['grand_total_usd'] = $payment->total_usd - $payment->discount_usd + $payment->vat_usd -  $payment->wht_usd - $payment->deposit_usd;

            /** convert timestamp */
            $time = Carbon::parse($payment->approved_at)->timestamp;
            $transformedPayment['approval_timestamp'] = floatval($time);

            return $transformedPayment;
        });

        /**sort by approval date */
        $sortedDESC = collect($tranformedPayments)->sortBy([['approval_timestamp', 'desc']]);

        return $sortedDESC->values()->all();
    }

    public function getAllAuditLogs()
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
            ->join('recordstatus', 'recordstatus.record_status_id', 'tasklist.req_status')
            ->where('tasklist.req_status', '!=', '001')
            ->whereRaw('(tasklist.req_recid in (select distinct(a.req_recid) from auditlog a where a.doer_email  = ?))', [$this->email])
            ->get();

        return $result;
    }
    public function mergeMultiArray($mail_array)
    {
        $singleArray = [];
        foreach ($mail_array as $childArray) {
            foreach ($childArray as $value) {
                $singleArray[] = $value;
            }
        }
        return $singleArray;
    }
    public function hasProcureByEmails()
    {

       /** procurement team */
        if (Session::get('is_procurement') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_PROCUREMENT')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        elseif (Session::get('is_markating') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_MARKETING')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        if (Session::get('is_admin_team') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_ADMINISTRATION')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        elseif (Session::get('PLD_team') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_LEARNING_PEOPLE')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        elseif (Session::get('is_infra_team') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_INFRA')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        elseif (Session::get('is_alternative_team') == '1') {
            $group_pro = ProcurementRecord::select('view_procurement_record_table.*')
                ->where('final_group', 'GROUP_ACD')->orderBy('view_procurement_record_table.updated_at','desc');
            return $group_pro;
        }
        return null;
    }

    public function groupDescription()
    {
        $groupId = $this->groupIds()->first();
        if ($groupId) {
            return $groupId->groupDescription;
        }

        return null;
    }

    public function rejectPayment(Payment $payment)
    {
        /** get payment body for update ytd expense */
        $paymentBodies = Paymentbody::where('req_recid', $payment->req_recid)->get();

        /**get budget history for payment */
        $budgetHistories = Budgethistory::where('req_recid', $payment->req_recid)->get();
        $paymentBodyIndex = 0;
        foreach ($budgetHistories as $budgetHistory) {
            /**check alternative code */
            $alternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($alternativeCode) {
                $totalBudgetRmain = $alternativeCode->payment_remaining + $budgetHistory->alternative_amount_use;
                $total = $totalBudgetRmain >= 0 ? $totalBudgetRmain : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /**check budget code */
            $budgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($budgetCode) {
                $totalBudgetRmain = $budgetCode->payment_remaining + $budgetHistory->budget_amount_use;
                $total = $totalBudgetRmain >= 0 ? $totalBudgetRmain : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /** recalculate ytd expense */
            $totalBudgetYTDExpense = 0;
            $totalAlternativeBudgetYTDExpens = 0;

            $updatedBudgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($updatedBudgetCode) {
                $totalBudgetYTDExpense = $updatedBudgetCode->total - $updatedBudgetCode->payment_remaining;
            }

            $updatedAlternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($updatedAlternativeCode) {
                $alternativeCode->refresh();
                $totalAlternativeBudgetYTDExpens =  $updatedAlternativeCode->total - $updatedAlternativeCode->payment_remaining;
            }

            try {
                $paymentBody =  (object)$paymentBodies[$paymentBodyIndex];
            } catch (Exception $e) {
                continue;
            }
            $totalYTDExpense = $totalBudgetYTDExpense + $totalAlternativeBudgetYTDExpens;
            Paymentbody::where('id', $paymentBody->id)->update(['ytd_expense' => $totalYTDExpense,]);

            $paymentBodyIndex++;
        }

        return $budgetHistories;
    }

    public function assignPaymentBack(Payment $payment)
    {
        /** get payment body for update ytd expense */
        $paymentBodies = Paymentbody::where('req_recid', $payment->req_recid)->get();

        /**get budget history for payment */
        $budgetHistories = Budgethistory::where('req_recid', $payment->req_recid)->get();
        $paymentBodyIndex = 0;
        foreach ($budgetHistories as $budgetHistory) {
            /**check alternative code */
            $alternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($alternativeCode) {
                $totalBudgetRmain = $alternativeCode->payment_remaining + $budgetHistory->alternative_amount_use;
                $total = $totalBudgetRmain >= 0 ? $totalBudgetRmain : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /**check budget code */
            $budgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($budgetCode) {
                $totalBudgetRmain = $budgetCode->payment_remaining + $budgetHistory->budget_amount_use;
                $total = $totalBudgetRmain >= 0 ? $totalBudgetRmain : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /** recalculate ytd expense */
            $totalBudgetYTDExpense = 0;
            $totalAlternativeBudgetYTDExpens = 0;

            $updatedBudgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($updatedBudgetCode) {
                $totalBudgetYTDExpense = $updatedBudgetCode->total - $updatedBudgetCode->payment_remaining;
            }

            $updatedAlternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($updatedAlternativeCode) {
                $alternativeCode->refresh();
                $totalAlternativeBudgetYTDExpens =  $updatedAlternativeCode->total - $updatedAlternativeCode->payment_remaining;
            }

            try {
                $paymentBody =  (object)$paymentBodies[$paymentBodyIndex];
            } catch (Exception $e) {
                continue;
            }
            $totalYTDExpense = $totalBudgetYTDExpense + $totalAlternativeBudgetYTDExpens;
            Paymentbody::where('id', $paymentBody->id)->update(['ytd_expense' => $totalYTDExpense,]);

            $paymentBodyIndex++;
        }

        return $budgetHistories;
    }

    public function submitPayment(Payment $payment)
    {
        /** get payment body for update ytd expense */
        $paymentBodies = Paymentbody::where('req_recid', $payment->req_recid)->orderBy('id', 'asc')->get();

        /** get current budget histories */
        $budgetHistories  = Budgethistory::where('req_recid', $payment->req_recid)->get();
        $paymentBodyIndex = 0;
        foreach ($budgetHistories as $budgetHistory) {
            /**update alternative code remaining balance */
            $alternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($alternativeCode) {
                $remainingBalance = $alternativeCode->payment_remaining - $budgetHistory->alternative_amount_use;
                $total = $remainingBalance >= 0 ? $remainingBalance : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /**update budget code remaining balance */
            $budgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($budgetCode) {
                $remainingBalance = $budgetCode->payment_remaining - $budgetHistory->budget_amount_use;
                if ($remainingBalance < 0) {
                    $remainingUsedBalance = $budgetHistory->budget_amount_use - $budgetCode->payment_remaining;
                    if ($alternativeCode) {
                        $alternativeRemainingBalance = $alternativeCode->payment_remaining - $remainingUsedBalance;
                        $total = $alternativeRemainingBalance >= 0 ? $alternativeRemainingBalance : 0;
                        Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code)
                            ->update([
                                'payment'           => $total,
                                'temp_payment'      => $total,
                                'payment_remaining' => $total,
                            ]);
                    }
                }

                $total = $remainingBalance >= 0 ? $remainingBalance : 0;
                Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code)
                    ->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
            }

            /** recalculate ytd expense */
            $totalBudgetYTDExpense = 0;
            $totalAlternativeBudgetYTDExpens = 0;

            $updatedBudgetCode = Budgetcode::firstWhere('budget_code', $budgetHistory->budget_code);
            if ($updatedBudgetCode) {
                $totalBudgetYTDExpense = $updatedBudgetCode->total - $updatedBudgetCode->payment_remaining;
            }

            $updatedAlternativeCode = Budgetcode::firstWhere('budget_code', $budgetHistory->alternative_budget_code);
            if ($updatedAlternativeCode) {
                $totalAlternativeBudgetYTDExpens =  $updatedAlternativeCode->total - $updatedAlternativeCode->payment_remaining;
            }

            try {
                $paymentBody =  (object)$paymentBodies[$paymentBodyIndex];
            } catch (Exception $e) {
                continue;
            }
            $totalYTDExpense = $totalBudgetYTDExpense + $totalAlternativeBudgetYTDExpens;
            Paymentbody::where('id', $paymentBody->id)->update(['ytd_expense' => $totalYTDExpense,]);

            $paymentBodyIndex++;
        }

        return $budgetHistories;
    }

    public function isCurrentApproverForProcurment(Procurement $procurement)
    {
        $taskList = Tasklist::where('req_recid', $procurement->req_recid)->whereIn('next_checker_group', [$this->email,'accounting'])->first();
        return $taskList;
    }


    public function approverProcurement(Procurement $procurement, $comment)
    {
        /** find reviewer approver */
        $reviewer = Reviewapprove::firstWhere('req_recid', $procurement->req_recid);

        /**check if approver is ceo */
        $isCEO = 0;
        $isDCEO = 0;
        if ($reviewer->hasApproverAsCEO()) {
            $isCEO = 1;
        }if($reviewer->hasApproverAsCEO()){
            $isDCEO = 1;
        }

        $taskList = Tasklist::firstWhere('req_recid', $procurement->req_recid);
        $currentStep = $taskList->step_number;

        /** find and alert to next approver */
        $nexApprover = $this->findNextApproverForProcurment($procurement, $taskList, $reviewer, $currentStep, $isCEO);
        $approver    = (object)$nexApprover;
     
        $nextApprover = '';
        if ($approver->next_checker_group) {
            $nextApprover = $approver->next_checker_group;
        } else {
            $nextStep = $currentStep + 1;
            $nexApprover = $this->findNextApproverForProcurment($procurement, $taskList, $reviewer, $nextStep, $isCEO);
            $approver    = (object)$nexApprover;
            $nextApprover = $approver->next_checker_group;
        }
        Tasklist::where('req_recid', $procurement->req_recid)->update([
            'next_checker_group' => $nextApprover,
            'step_number'        => $approver->step_number,
            'req_status'         =>  $approver->is_last_approver ? RequestStatusEnum::Approved() : RequestStatusEnum::Pending()
        ]);
        $doerRole = $taskList->checkRole($taskList);
        $doerAction = $taskList->checkAction($doerRole);
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'step_action'          => $currentStep,
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);
    }

    public function assignProcurmentBack(Procurement $procurement, $comment, $grand_total)
    {
        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $procurement->req_recid);
        $doerRole = $taskList->checkRole($taskList);
        /**update Tasklist */
        Tasklist::where('req_recid', $procurement->req_recid)->update([
            'next_checker_group'            => $taskList->req_email,
            'next_checker_role'             => '1',
            'step_number'                   => '1',
            'assign_back_by'                => $this->email,
            'by_role'                       => $taskList->next_checker_role,
            'by_step'                       => $taskList->step_number,
            'old_amout_after_assign_bank'   => $grand_total,
        ]);

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Assign(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'step_action'          => $taskList->step_number,
            'doer_role'            => $doerRole,
            'doer_action'          => 'Assigned Back Request'
        ]);
    }

    public function queryProcurement(Procurement $procurement, $comment, $userEmail)
    {
        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $procurement->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $procurement->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
       
        /**get detail from tasklist */
        $taskList = Tasklist::where('req_recid',$procurement->req_recid)->first();
        $doerRole = $taskList->checkRole($taskList);
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'step_action'         => $taskList->step_number,
            'doer_role'            => $userEmail==$taskList->req_email?'Requester':$doerRole,
            'doer_action'          => 'Queried Request'
        ]);
    }

    public function assignProcurementBackToCFO(Procurement $procurement, $comment)
    {
        /** find user as CFO */
        $groupId = Groupid::firstWhere([['group_id', 'GROUP_CFO'],['is_cfo', '1']]);
        if ($groupId) {
            Tasklist::where('req_recid', $procurement->req_recid)->update([
                'next_checker_group' => $groupId->email,
                'next_checker_role'  => '1',
                'step_number'        => '3',
                'req_status'         => RequestStatusEnum::Pending(),
                'insufficient'        => null,
            ]);
        }
        $taskList = Tasklist::firstWhere('req_recid', $procurement->req_recid);
        $doerRole = $taskList->checkRole($taskList);
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'step_action'         => $taskList->step_number,
            'doer_role'            => $doerRole,
            'doer_action'          => 'Assigned Back Request'
        ]);
    }

    public function transferProcurement(Procurement $procurement, $comment, $transferTo)
    {
        $approver = explode('/', $transferTo);
        $email = $approver[0];
        $role  = $approver[1];
        // find group for transfer to
        $group_transfer_to = Groupid::join('groupdescription','groupdescription.group_id','groupid.group_id')->where('email',$email)->where('is_procurement_record','Y')->first();
       
        $taskList = Tasklist::firstWhere('req_recid', $procurement->req_recid);
        $doerRole = $taskList->checkRole($taskList);
        /** update tasklist */
        Tasklist::where('req_recid', $procurement->req_recid)->update([
            'next_checker_group' => $email,
            'next_checker_role'  => $role,
        ]);

        /** update review approve */
        Reviewapprove::where('req_recid', $procurement->req_recid)
                        ->update(['final' => $email,'final_group' => $group_transfer_to->group_id]); 

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Transfer(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => 'Transfered Request'
        ]);
    }

    public function rejectProcurment(Procurement $procurement, $comment)
    {
        /**Update task list */
        Tasklist::where('req_recid', $procurement->req_recid)->update([
            'next_checker_group' => 'close',
            'next_checker_role' => 'close',
            'req_status' => RequestStatusEnum::Rejected(),
        ]);
        $taskList = Tasklist::firstWhere('req_recid', $procurement->req_recid);
        $doerRole = $taskList->checkRole($taskList);

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $procurement->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ProcurementRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => 'Rejected Request'
        ]);
    }

    public function findNextApproverForProcurment(Procurement $procurement, Tasklist $taskList, Reviewapprove $reviewer, $currentStep, $isCEO)
    {
        $nextStep = $currentStep + 1;
        /** find flow configure */
        $bid = $procurement->bid;
        if ($taskList->isWithinBudget()) {
            $total = $procurement->getTotalRequestAmount(); 
            $amountRequest = '';
            if ($bid == 'yes') {
                $amountRequest = '<=3000';
            } else {
                if ($total <= 3000) {
                    $amountRequest = '<=3000';
                } elseif ($total > 3000 and $total < 30000) {
                    $amountRequest = '<=5000';
                } else {
                    $amountRequest = '>5000';
                }
            }

            if ($bid == "yes" and $isCEO == 0) {
                $flowConfigure = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('step_number', $nextStep)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', 'Y')
                    ->where('version', 2)
                    ->first();
            } else {
                $flowConfigure = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('step_number', $nextStep)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', null)
                    ->where('version', 2)
                    ->first();
            }
        } else {
            $flowConfigure = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                ->where('within_budget', 'N')
                ->where('step_number', $nextStep)
                ->where('version', 2)
                ->first();
        }
        if (!$flowConfigure && $nextStep > 1) {
            return [
                'next_checker_group' => 1,
                'step_number'        => $currentStep,
                'is_last_approver'   => true
            ];
        }
        if ($flowConfigure->checker == 'co_approver') {
            return [
                'doer_role'          => 'Co-Approver',
                'doer_action'        => 'Approved request',
                'next_checker_group' => $reviewer->co_approver,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'first_reviewer') {
            return [
                'doer_role'          => 'First Reviewer',
                'doer_action'        => 'Reviewed request',
                'next_checker_group' => $reviewer->review,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'second_reviewer') {
            if ($reviewer->second_review) {
                return [
                    'doer_role'          => 'Second Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => $reviewer->second_review,
                    'step_number'        => $nextStep,
                    'is_last_approver'   => false
                ];
            }else {
                return [
                    'doer_role'          => 'Accounting Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => 'accounting',
                    'step_number'        => $nextStep + 3,
                    'is_last_approver'   => false
                ];
            }
        }
        if ($flowConfigure->checker == 'third_reviewer') {
            if ($reviewer->third_review) {
                return [
                    'doer_role'          => 'Third Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => $reviewer->third_review,
                    'step_number'        => $nextStep,
                    'is_last_approver'   => false
                ];
            } else {
                return [
                    'doer_role'          => 'Accounting Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => 'accounting',
                    'step_number'        => $nextStep + 2,
                    'is_last_approver'   => false
                ];
            }
        }
        if ($flowConfigure->checker == 'forth_reviewer') {
            if ($reviewer->fourth_reviewer) {
                return [
                    'doer_role'          => 'Fourth Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => $reviewer->fourth_reviewer,
                    'step_number'        => $nextStep,
                    'is_last_approver'   => false
                ];
            } else {
                return [
                    'doer_role'          => 'Accounting Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => 'accounting',
                    'step_number'        => $nextStep + 1,
                    'is_last_approver'   => false
                ];
            }
        }
        //**accounting reviewer */
        if ($flowConfigure->checker == 'accounting') {
            return [
                'doer_role'          => 'Accounting Reviewer',
                'doer_action'        => 'Reviewed request',
                'next_checker_group' => $reviewer->accounting,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }
        //**procurement reviewer */
        if ($flowConfigure->checker == 'procurement') {
            return [
                'doer_role'          => 'Procerement Reviewer',
                'doer_action'        => 'Reviewed request',
                'next_checker_group' => $reviewer->procurement,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }
        if ($flowConfigure->checker == 'budget_owner') {
            return [
                'doer_role'          => 'Budget Owner',
                'doer_action'        => 'Approved on Budget code',
                'next_checker_group' => $reviewer->budget_owner,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'md_office') {
            $groupId = Groupid::firstWhere('group_id', 'GROUP_MDOFFICE');
            return [
                'doer_role'          => 'Approver',
                'doer_action'        => 'Approved request',
                'next_checker_group' => $groupId->email,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'approver_cfo') {
            $groupId = Groupid::firstWhere([['group_id', 'GROUP_CFO'],['is_cfo', '1']]);
            return [
                'doer_role'          => 'CFO',
                'doer_action'        => 'Approved request',
                'next_checker_group' => $groupId->email,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'approver') {
            $isDCEO = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_DCEO')->where('groupid.email',$reviewer->approve)->where('groupid.status',1)->first();
            if($isDCEO){
                if($reviewer->dceo_approve == 0){
                    $isDCEOOffice = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_DCEO_OFFICE')->first();
                    Reviewapprove::where('req_recid',$procurement->req_recid)->update(['dceo_approve' => 1]);
                    return [
                        'doer_role'          => 'Reviewer',
                        'doer_action'        => 'Reviewed request',
                        'next_checker_group' => $isDCEOOffice->email,
                        'step_number'        => $currentStep,
                        'is_last_approver'   => false
                    ];
                }else{
                    return [
                        'doer_role'          => 'Reviewer',
                        'doer_action'        => 'Reviewed request',
                        'next_checker_group' => $reviewer->approve,
                        'step_number'        => $nextStep,
                        'is_last_approver'   => false
                    ];
                }
            }else{
                return [
                    'doer_role'          => 'Reviewer',
                    'doer_action'        => 'Reviewed request',
                    'next_checker_group' => $reviewer->approve,
                    'step_number'        => $nextStep,
                    'is_last_approver'   => false
                ];
            }
        }
       
        if ($flowConfigure->checker == 'approver_ceo') {
            $groupId = Groupid::firstWhere('group_id', 'GROUP_CEO');
            return [
                'doer_role'          => 'Approver',
                'doer_action'        => 'Approved request',
                'next_checker_group' => $groupId->email,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }

        if ($flowConfigure->checker == 'receiver' and $bid != 'yes') {
            return [
                'doer_role'          => 'Procure by',
                'doer_action'        => 'Received Request',
                'next_checker_group' => $reviewer->final,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }
        if ($flowConfigure->checker == 'receiver' and $bid == 'yes') {
            return [
                'doer_role'          => 'Procure by',
                'doer_action'        => 'Received Request',
                'next_checker_group' => $reviewer->final,
                'step_number'        => $nextStep,
                'is_last_approver'   => false
            ];
        }
    }

    public function isBelongToAdvanceForm(AdvanceForm $advanceForm)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $advanceForm->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }

    public function isBelongToClearAdvanceForm(ClearAdvanceForm $clearAdvanceForm)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $clearAdvanceForm->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }

    public function isBelongToBankPaymentVoucherForm(BankPaymentVoucher $bankPaymentVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $bankPaymentVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }
    public function isBelongToBankVoucherForm(BankVoucher $bankVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $bankVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }
    public function isBelongToCashPaymentVoucherForm(CashPaymentVoucher $CashPaymentVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $CashPaymentVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }
    public function isBelongToCashReceiptVoucherForm(CashReceiptVoucher $CashReceiptVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $CashReceiptVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }
    public function isBelongToBankReceiptVoucherForm(BankReceiptVoucher $bankReceiptVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $bankReceiptVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }
    public function isBelongJournalVoucherForm(JournalVoucher $journalVoucher)
    {
        /** find requester */
        $requester = Requester::firstWhere('req_recid', $journalVoucher->req_recid);
        if ($this->email !== $requester->req_email) {
            return false;
        }
        return true;
    }

    public function isUserIsPendingForAdvanceForm(AdvanceForm $advanceForm)
    {
        /** find requester */
        $taskList = Tasklist::firstWhere('req_recid', $advanceForm->req_recid);
        if (!$taskList) {
            return false;
        }

        if ($taskList->next_checker_group == 'accounting') {
            $groupId = Groupid::where('email', $this->email)->where('group_id', 'GROUP_ACCOUNTING')->first();
            if ($groupId) {
                return true;
            } else {
                return false;
            }
        }

        if ($taskList->next_checker_group == $this->email) {
            return true;
        }

        return false;
    }

    public function approveClearAdvanceForm(ClearAdvanceForm $clearAdvanceForm, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $clearAdvanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $clearAdvanceForm->req_recid);
        $withinBudget = $taskList->within_budget;
        $totalAmountRequest = $clearAdvanceForm->total_amount_usd;
        $flowAmountRequest = $clearAdvanceForm->getRequestAmountForFlowConfig($totalAmountRequest);
        $nextStep = $taskList->step_number + 1;

        /**check if requester is accounting group */
        $isAccountingTeam = false;
        $groupId = Groupid::where('email', $taskList->req_email)->where('group_id', 'GROUP_ACCOUNTING')->first();
        if ($groupId) {
            $isAccountingTeam = true;
        }

        /**find flow config */
        $flowConfig = Flowconfig::where('req_name', FormTypeEnum::ClearAdvanceFormRequest())
            ->where('within_budget', $withinBudget)
            ->where('amount_request', $flowAmountRequest)
            ->where('step_number', $nextStep)
            ->where('is_accounting_team', $isAccountingTeam)
            ->where('version', '2')
            ->first();
        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }
        /** check if next approver is second reviewer */
        if ($flowConfig->checker == 'second_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            if($reviewApprover->second_review){
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->second_review,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is third reviewer */
        if ($flowConfig->checker == 'third_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            if($reviewApprover->third_review){
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->third_review,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is fourth reviewer */
        if ($flowConfig->checker == 'fourth_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            if($reviewApprover->fourth_reviewer){
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->fourth_reviewer,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting') {
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }


        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_finance') {
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => 'accounting',
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is general approver */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is general approver */
        if ($flowConfig->checker == 'approver_cfo') {
            $approver = Groupid::firstWhere([['group_id', 'GROUP_CFO'],['is_cfo', '1']]);
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is md office */
        if ($flowConfig->checker == 'md_office') {
            $approver = Groupid::firstWhere('group_id', 'GROUP_MDOFFICE');
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is ceo */
        if ($flowConfig->checker == 'approver_ceo') {
            $approver = Groupid::firstWhere('group_id', 'GROUP_CEO');
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $clearAdvanceForm->req_recid);
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }

    public function approveBankPaymentVoucherForm(BankPaymentVoucher $bankPaymentVoucher, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $bankPaymentVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $bankPaymentVoucher->getFlowConfigByAmount($bankPaymentVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            $request_link = $bankPaymentVoucher->ref_no;
            $this->updateSingleRequestLink($request_link, $comment);
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }
        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $bankPaymentVoucher->req_recid);
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_voucher') {
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function approveBankVoucherForm(BankVoucher $bankVoucher, $comment)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString()
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $bankVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $bankVoucher->getFlowConfigByAmount($bankVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }

        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $bankVoucher->req_recid);
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting') {
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function approveCashPaymentVoucherForm(CashPaymentVoucher $cashPaymentVoucher, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $cashPaymentVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $cashPaymentVoucher->getFlowConfigByAmount($cashPaymentVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            $request_link = $cashPaymentVoucher->ref_no;
            $this->updateSingleRequestLink($request_link, $comment);
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }

        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $cashPaymentVoucher->req_recid);
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_voucher') {
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function approveCashReceiptVoucherForm(CashReceiptVoucher $cashReceiptVoucher, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $cashReceiptVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $cashReceiptVoucher->getFlowConfigByAmount($cashReceiptVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }

        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $cashReceiptVoucher->req_recid);
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_voucher') {
            $request_link = $cashReceiptVoucher->ref_no;
            $this->updateSingleRequestLink($request_link, $comment);
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function approveBankReceiptVoucherForm(BankReceiptVoucher $bankReceiptVoucher, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $bankReceiptVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $bankReceiptVoucher->getFlowConfigByAmount($bankReceiptVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            $request_link = $bankReceiptVoucher->ref_no;
            $this->updateSingleRequestLink($request_link, $comment);
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }

        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $bankReceiptVoucher->req_recid);
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_voucher') {
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function approveJournalVoucherForm(JournalVoucher $journalVoucher, $comment, $doerRole, $doerAction)
    {

        /** log current user approve this journal  */
        Auditlog::create([
            'req_recid'            => $journalVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);


        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $journalVoucher->req_recid);
        $nextStep = $taskList->step_number + 1;


        /**find flow config */
        $flows = $journalVoucher->getFlowConfigByAmount($journalVoucher->total_for_approval_usd);
        $flowConfig = collect($flows)->firstWhere('step', $nextStep);


        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }

        /** reviewer */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $journalVoucher->req_recid);
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_voucher') {
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }

    public function approveAdvanceForm(AdvanceForm $advanceForm, $comment, $doerRole, $doerAction)
    {

        /**alert to next approver */
        $taskList = Tasklist::firstWhere('req_recid', $advanceForm->req_recid);
        $withinBudget = $taskList->within_budget;
        $totalAmountRequest = $advanceForm->total_amount_usd;
        $flowAmountRequest = $advanceForm->getRequestAmountForFlowConfig($totalAmountRequest);
        $nextStep = $taskList->step_number + 1;

        /**check if requester is accounting group */
        $isAccountingTeam = false;
        $groupId = Groupid::where('email', $taskList->req_email)->where('group_id', 'GROUP_ACCOUNTING')->first();
        if ($groupId) {
            $isAccountingTeam = true;
        }

        /**find flow config */
        $flowConfig = Flowconfig::where('req_name', FormTypeEnum::AdvanceFormRequest())
            ->where('within_budget', $withinBudget)
            ->where('amount_request', $flowAmountRequest)
            ->where('step_number', $nextStep)
            ->where('is_accounting_team', $isAccountingTeam)
            ->where('version', '2')
            ->first();
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $advanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::AdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** check if we cannot found flow because of step it mean that current user is last approver */
        if (!$flowConfig && $nextStep > 1) {
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $nextStep,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            return $taskList;
        }
        /** check if next approver is second reviewer */
        if ($flowConfig->checker == 'second_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            if($reviewApprover->second_review){
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->second_review,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is third reviewer */
        if ($flowConfig->checker == 'third_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            if($reviewApprover->third_review){
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->third_review,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is fourth reviewer */
        if ($flowConfig->checker == 'fourth_reviewer') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            if($reviewApprover->fourth_reviewer){
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => $reviewApprover->fourth_reviewer,
                    'next_checker_role'  => '2',
                    'step_number'        => $flowConfig->step_number,
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }else{
                Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                    'next_checker_group' => 'accounting',
                    'next_checker_role'  => '2',
                    'step_number'        => '5',
                    'req_status'         => RequestStatusEnum::Approve()
                ]);
            }
            return $taskList;
        }
        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting') {
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $flowConfig->checker,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }


        /** check if next approver is accounting team */
        if ($flowConfig->checker == 'accounting_finance') {
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => 'accounting',
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is general approver */
        if ($flowConfig->checker == 'approver') {
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $reviewApprover->approve,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is general approver */
        if ($flowConfig->checker == 'approver_cfo') {
            $approver = Groupid::firstWhere([['group_id', 'GROUP_CFO'],['is_cfo', '1']]);
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is md office */
        if ($flowConfig->checker == 'md_office') {
            $approver = Groupid::firstWhere('group_id', 'GROUP_MDOFFICE');
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }

        /** check if next approver is ceo */
        if ($flowConfig->checker == 'approver_ceo') {
            $approver = Groupid::firstWhere('group_id', 'GROUP_CEO');
            $reviewApprover = Reviewapprove::firstWhere('req_recid', $advanceForm->req_recid);
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $approver->email,
                'next_checker_role'  => '2',
                'step_number'        => $flowConfig->step_number,
                'req_status'         => RequestStatusEnum::Approve()
            ]);
            return $taskList;
        }
    }
    public function rejectBankPaymentVoucherForm(BankPaymentVoucher $cashPaymentVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectBankVoucherForm(BankVoucher $bankVoucher, $comment)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString()
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectCashPaymentVoucherForm(CashPaymentVoucher $cankPaymentVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cankPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
            
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $cankPaymentVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectCashReceiptVoucherForm(CashReceiptVoucher $cankReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cankReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $cankReceiptVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectBankReceiptVoucherForm(BankReceiptVoucher $bankReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectJournalVoucherForm(JournalVoucher $journalVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $journalVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }
    public function rejectClearAdvanceForm(ClearAdvanceForm $ClearAdvanceForm, $comment, $doerRole, $doerAction) 
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $ClearAdvanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** update task list to reject */
        Tasklist::where('req_recid', $ClearAdvanceForm->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);
    }

    public function rejectAdvanceForm(AdvanceForm $advanceForm, $comment, $doerRole, $doerAction)
    {
        /** update task list to reject */
        Tasklist::where('req_recid', $advanceForm->req_recid)->update([
            'next_checker_group' => 'close',
            'req_status'         => RequestStatusEnum::Rejected()
        ]);

        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $advanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Rejected(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::AdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction,
        ]);
    }
    public function assignBankPaymentVoucherFormBack(BankPaymentVoucher $bankPaymentVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $bankPaymentVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignBankVoucherFormBack(BankVoucher $bankVoucher, $comment)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString()
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $bankVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignCashPaymentVoucherFormBack(CashPaymentVoucher $cashPaymentVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $cashPaymentVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignCashReceiptVoucherFormBack(CashReceiptVoucher $cashReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $cashReceiptVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignBankReceiptVoucherFormBack(BankReceiptVoucher $bankReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $bankReceiptVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignJournalVoucherFormBack(JournalVoucher $journalVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $journalVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $journalVoucher->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }
    public function assignClearAdvanceFormBack(ClearAdvanceForm $clearAdvanceForm, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $clearAdvanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $clearAdvanceForm->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }

    public function assignAdvanceFormBack(AdvanceForm $advanceForm, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $advanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::AssignBack(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::AdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction,
        ]);

        /** assign procurremt back to requester */
        $taskList = Tasklist::firstWhere('req_recid', $advanceForm->req_recid);
        if ($taskList) {
            /**update Tasklist */
            Tasklist::where('req_recid', $advanceForm->req_recid)->update([
                'next_checker_group' => $taskList->req_email,
                'next_checker_role'  => '1',
                'step_number'        => '1',
                'assign_back_by'     => $this->email,
                'by_role'            => $taskList->next_checker_role,
                'by_step'            => $taskList->step_number,
            ]);
        }
    }

    public function queryBankPaymentVoucherForm(BankPaymentVoucher $bankPaymentVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $bankPaymentVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }
    public function queryBankVoucherForm(BankVoucher $bankVoucher, $comment)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString()
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $bankVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }
    public function queryCashPaymentVoucherForm(CashPaymentVoucher $cashPaymentVoucher, $commen, $doerRole, $doerActiont)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashPaymentVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashPaymentVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $cashPaymentVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }
    public function queryCashReceiptVoucherForm(CashReceiptVoucher $cashReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $cashReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $cashReceiptVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }
    public function queryBankReceiptVoucherForm(BankReceiptVoucher $bankReceiptVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $bankReceiptVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::BankReceiptVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $bankReceiptVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }
    public function queryJournalVoucherForm(JournalVoucher $journalVoucher, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $journalVoucher->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::JournalVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $journalVoucher->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }

    public function queryClearAdvanceForm(ClearAdvanceForm $clearAdvanceForm, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $clearAdvanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::ClearAdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $clearAdvanceForm->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }

    public function queryAdvanceForm(AdvanceForm $advanceForm, $comment, $doerRole, $doerAction)
    {
        /** log current user approve this procurement  */
        Auditlog::create([
            'req_recid'            => $advanceForm->req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Query(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::AdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doerRole,
            'doer_action'          => $doerAction,
        ]);

        /**find current user tasklist */
        $taskList = Tasklist::where('req_email', $this->email)->where('req_status', RequestStatusEnum::Query())->first();
        if ($taskList) {
            Tasklist::where('req_recid', $advanceForm->req_recid)->update(['req_status' => RequestStatusEnum::Pending()]);
        } else {
            Tasklist::where('req_recid', $advanceForm->req_recid)->update(['req_status' => RequestStatusEnum::Query()]);
        }
    }

    public function isAccountingTeam()
    {
        $groupId = Groupid::where('email', $this->email)->where('group_id', 'GROUP_ACCOUNTING')->first();
        return $groupId;
    }

    public function countTotalAdvanceRecord()
    {
        $totalAdvanceRecord = ViewAdvanceRecord::count();
        return $totalAdvanceRecord;
    }
    public function requestType($request)
    {
        $slice = Str::before($request, '-');
        if ($slice == 'RP') {
            $request_type = FormTypeEnum::PaymentRequest();
        } elseif ($slice == 'ADV') {
            $request_type = FormTypeEnum::AdvanceFormRequest();
        } else {
            $request_type = FormTypeEnum::ClearAdvanceFormRequest();
        }
        return $request_type;
    }
    
    public function updateSingleRequestLink($request_link, $comment)
    {
        if(!$request_link){
            return false;
        }
        $string_contains_request_link = Str::contains($request_link, ',');
        if ($string_contains_request_link === false) {
            $tasklist_link = Tasklist::Where('req_recid', $request_link)->first();
            $email_requester = $tasklist_link->req_email;
            $type_of_request = $this->requestType($request_link);
            Tasklist::where('req_recid', $request_link)->update([
                'next_checker_group' =>  '1',
                'step_number'        =>  $tasklist_link->req_status == '005'?$tasklist_link->step_number:$tasklist_link->step_number + 1,
                'req_status'         =>  RequestStatusEnum::Approved()
            ]);
            if($tasklist_link->next_checker_group != 1){
                Auditlog::create([
                    'req_recid'            =>  $request_link,
                    'doer_email'           => $this->email,
                    'doer_name'            => "{$this->firstname} {$this->lastname}",
                    'doer_branch'          => $this->department,
                    'doer_position'        => $this->position,
                    'activity_code'        => ActivityCodeEnum::Approved(),
                    'activity_description' => $comment,
                    'activity_form'        => $type_of_request,
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                    'doer_role'            => 'Payment Process',
                    'doer_action'          => 'Payment confirmed'
                ]);
            }
            $request_subject = Requester::where('req_recid', $request_link)->first();
            $content       = 'Payment Request has been approved.';
            $name_requester = $tasklist_link->req_name;
            $branch_requester = $tasklist_link->req_branch;
            $position_requester = $tasklist_link->req_position;
            $send_mail    = new Sendemail();
            if($type_of_request == 2){
                $subject      = 'Payment Request';
            }elseif($type_of_request == 3){
                $subject      = 'Advance Request';
            }else{
                $subject      = 'Clear Advance Request';
            }
            
            $return_email = $send_mail->sendEmailProcurementRequest($content, $request_link, $name_requester, $branch_requester, $position_requester, $subject, $email_requester, $email_requester, $comment, $request_subject->subject);
            if ($return_email == 'fail') {
                Session::flash('success', 'Success but no email send');
            } else {
                Session::flash('success');
            }
        }else{
            $request_link_multi = explode(',', $request_link);
            foreach($request_link_multi as $data){
                $tasklist_link = Tasklist::firstWhere('req_recid', $data);
                $email_requester = $tasklist_link->req_email;
                $type_of_request = $this->requestType($data);
                Tasklist::where('req_recid', $data)->update([
                    'next_checker_group' =>  '1',
                    'step_number'        =>  $tasklist_link->req_status == '005'?$tasklist_link->step_number:$tasklist_link->step_number + 1,
                    'req_status'         =>  RequestStatusEnum::Approved()
                ]);
                if($tasklist_link->next_checker_group != 1){
                    Auditlog::create([
                        'req_recid'            =>  $data,
                        'doer_email'           => $this->email,
                        'doer_name'            => "{$this->firstname} {$this->lastname}",
                        'doer_branch'          => $this->department,
                        'doer_position'        => $this->position,
                        'activity_code'        => ActivityCodeEnum::Approved(),
                        'activity_description' => $comment,
                        'activity_form'        => $type_of_request,
                        'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                        'doer_role'            => 'Payment Process',
                        'doer_action'          => 'Payment confirmed'
                    ]);
                }
                $request_subject = Requester::where('req_recid', $data)->first();
                $content       = 'Your Request has been approved.';
                $name_requester = $tasklist_link->req_name;
                $branch_requester = $tasklist_link->req_branch;
                $position_requester = $tasklist_link->req_position;
                $send_mail    = new Sendemail();
                if($type_of_request == 2){
                    $subject      = 'Payment Request';
                }elseif($type_of_request == 3){
                    $subject      = 'Advance Request';
                }else{
                    $subject      = 'Clear Advance Request';
                }
                
                $return_email = $send_mail->sendEmailProcurementRequest($content, $data, $name_requester, $branch_requester, $position_requester, $subject, $email_requester, $email_requester, $comment, $request_subject->subject);
                if ($return_email == 'fail') {
                    Session::flash('success', 'Success but no email send');
                } else {
                    Session::flash('success');
                }
            }
        }
        
    }
    public function authorizeBankPaymentVoucherForm(BankPaymentVoucher $bankPaymentVoucher, $comment){
        $request_link = $bankPaymentVoucher->ref_no;
        $req_recid = $bankPaymentVoucher->req_recid;
        $this->lineAuthorize($request_link,$req_recid,$comment);
        return true;
    }
    public function authorizeJournalVoucherForm(JournalVoucher $journalVoucher, $comment){
        $request_link = $journalVoucher->ref_no;
        $req_recid = $journalVoucher->req_recid;
        $this->lineAuthorize($request_link,$req_recid,$comment);
        return true;
    }
    public function authorizeVoucherBankReciptForm(BankReceiptVoucher $BankReceiptVoucher, $comment){
        $request_link = $BankReceiptVoucher->ref_no;
        $req_recid = $BankReceiptVoucher->req_recid;
        $this->lineAuthorize($request_link,$req_recid,$comment);
        return true;
    }
    public function lineAuthorize($request_link,$req_recid,$comment){
        $string_contains_request_link = Str::contains($request_link, ',');
         // update status for request
         $tasklist_request = Tasklist::firstWhere('req_recid', $req_recid);
         $last_approved_link = Auditlog::where('req_recid',$request_link)->orderBy('id','desc')->first();
         //user assing to line for authorized  
        Tasklist::where('req_recid', $req_recid)->update([
            'next_checker_group' =>  'accounting_voucher',
            'step_number'        =>  $tasklist_request->step_number - 1,
            'req_status'         =>  RequestStatusEnum::Approve(),
            'change_status_request_to'         =>  null
        ]);
        Auditlog::create([
            'req_recid'            =>  $req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => $tasklist_request->req_type,
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => 'Approver',
            'doer_action'          => 'Approved'
        ]);
        // update status for link request
        if(!$request_link){
            return false;
        }
        if ($string_contains_request_link === false) {
            $tasklist_link = Tasklist::firstWhere('req_recid', $request_link);
            $last_approved_link = Auditlog::where('req_recid',$request_link)->orderBy('id','desc')->first();
            $type_of_request = $this->requestType($request_link);
            Tasklist::where('req_recid', $request_link)->update([
                'next_checker_group' =>  'accounting',
                'step_number'        =>  $tasklist_link->step_number - 1,
                'req_status'         =>  RequestStatusEnum::Approve()
            ]);
            
            Auditlog::create([
                'req_recid'            =>  $request_link,
                'doer_email'           => $this->email,
                'doer_name'            => "{$this->firstname} {$this->lastname}",
                'doer_branch'          => $this->department,
                'doer_position'        => $this->position,
                'activity_code'        => ActivityCodeEnum::Approved(),
                'activity_description' => $comment,
                'activity_form'        => $type_of_request,
                'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                'doer_role'            => 'Approver',
                'doer_action'          => 'Approved'
            ]);
        }else{
            $request_link_multi = explode(',', $request_link);
            foreach($request_link_multi as $data){
                $last_approved_link = Auditlog::where('req_recid',$data)->orderBy('id','desc')->first();
                $tasklist_link = Tasklist::firstWhere('req_recid', $data);
                $type_of_request = $this->requestType($data);
                Tasklist::where('req_recid', $data)->update([
                    'next_checker_group' =>  'accounting',
                    'step_number'        => $tasklist_link->step_number - 1,
                    'req_status'         =>  RequestStatusEnum::Approve()
                ]);
                Auditlog::create([
                    'req_recid'            =>  $data,
                    'doer_email'           => $this->email,
                    'doer_name'            => "{$this->firstname} {$this->lastname}",
                    'doer_branch'          => $this->department,
                    'doer_position'        => $this->position,
                    'activity_code'        => ActivityCodeEnum::Approved(),
                    'activity_description' => $comment,
                    'activity_form'        => $type_of_request,
                    'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
                    'doer_role'            => 'Approver',
                    'doer_action'          => 'Approved'
                ]);
            }
        }
    }
    public function changeStatusRequest($req_recid,$comment,$type_request){
        if($type_request == 1){
            $bankPayment = BankPaymentVoucher::where('req_recid',$req_recid)->first();
            $request_link = $bankPayment->ref_no;
        }elseif($type_request == 2){
            $bankReceipt = BankReceiptVoucher::where('req_recid',$req_recid)->first();
            $request_link = $bankReceipt->ref_no; 
        }elseif($type_request == 3){
            $bankReceipt = JournalVoucher::where('req_recid',$req_recid)->first();
            $request_link = $bankReceipt->ref_no; 
        }else{
            $bankPayment = CashPaymentVoucher::where('req_recid',$req_recid)->first();
            $request_link = $bankPayment->ref_no;
        }
        $last_approved = Auditlog::where('req_recid',$req_recid)->orderBy('id','desc')->first();
        $string_contains_request_link = Str::contains($request_link, ',');
        // update status for request
        $tasklist_request = Tasklist::firstWhere('req_recid', $req_recid);
        //user assing to line for authorized
        Tasklist::where('req_recid', $req_recid)->update([
            'next_checker_group' =>  's.sophy@princebank.com.kh',
            'step_number'        =>  $tasklist_request->step_number,
            'req_status'         =>  RequestStatusEnum::Approved(),
            'change_status_request_to'         =>  $this->email
        ]);
        Auditlog::create([
            'req_recid'            =>  $req_recid,
            'doer_email'           => $this->email,
            'doer_name'            => "{$this->firstname} {$this->lastname}",
            'doer_branch'          => $this->department,
            'doer_position'        => $this->position,
            'activity_code'        => ActivityCodeEnum::Approved(),
            'activity_description' => $comment,
            'activity_form'        => $tasklist_request->req_type,
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => 'Requester',
            'doer_action'          => 'Changed Request'
        ]);
    }
    public function RequestNotApprove ($requests){
       
        $request_link_multi = explode(',', $requests);
        foreach($request_link_multi as $request){
            $within_ornot = Paymentbody::where(['req_recid' => $request, 'within_budget_code' => 'N'])->first();
            $total_all = Paymentbody::where('req_recid', $request)->get();
            $request_reject = Tasklist::where('req_recid', $request)->first();
            $type_form = substr($request,0,2);
            /**if ref request was rejected */
            if( $request_reject->req_status == '004'){
                return true ;
            }
            if($type_form != "PR" and $request_reject->req_status == '003'){
                return true ;
            }

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
                if(!empty($request_reject->is_new_flow == 1)){
                    if (!empty($within_ornot)) {
                        if ($max_spent == '<=10000' or $max_spent == '<=50000') {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','9')->first();
                        }else {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','8')->first();
                        }
                    } else {
                        if ($max_spent == '<=10000' or $max_spent == '<=50000') {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                                            ->where('step_number','>','6')->first();
                        }else {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','7')->first();
                        }
                    }
                }else{
                    if (!empty($within_ornot)) {
                        if ($max_spent == '<=10000' or $max_spent == '<=50000') {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','6')->first();
                        }else {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','5')->first();
                        }
                    } else {
                        if ($max_spent == '<=10000' or $max_spent == '<=50000') {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                                            ->where('step_number','>','3')->first();
                        }else {
                            $tasklist_link = Tasklist::Where('req_recid', $request)
                            ->where('step_number','>','4')->first();
                        }
                    }
                }
                

            if(!$tasklist_link){
                return false;
            }
        }
        return true ;
    }
    public function requestLink($request_id){
         /** find request id */
         $contains = Str::contains($request_id , [',']);
         $rp_ref_no_pr=array();
         // multi request
         if($contains == true){
             $merge_req = explode(',',$request_id,10);
             foreach($merge_req as $req){
                $type_form = substr($req,0,2);
                $type_form_ADV = substr($req,0,3);
                 $cryp_advance = Crypt::encrypt($req . '___no');
                 if($type_form === "PR"){
                    $url_advance  = url("form/procurement/detail/{$cryp_advance}");
                 }else{
                    $url_advance  = url("form/advances/detail/{$cryp_advance}");
                 }
                 $rp_ref_no = [
                         'href' => $url_advance,
                         'value' => $req
                 ];
                 $rp_ref_no_pr[] = $rp_ref_no;
             }
         }else{
             $string_req = $request_id;
             $cryp_advance = Crypt::encrypt($string_req . '___no');
             $type_form = substr($string_req,0,2);
             $type_form_ADV = substr($string_req,0,3);
            //  $url_advance  = url("form/procurement/detail/{$cryp_advance}");
             if($type_form === "PR"){
                $url_advance  = url("form/procurement/detail/{$cryp_advance}");
             }else{
                $url_advance  = url("form/advances/detail/{$cryp_advance}");
             }
             $rp_ref_no_pr[] = [
                 'href' => $url_advance,
                 'value' => $string_req
             ];
         }
         return $rp_ref_no_pr;
    }
    
}
