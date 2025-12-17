<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\ActivityCodeEnum;
use App\Enums\BudgetEnum;
use App\Enums\ActionEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Myclass\Sendemail;
use App\Traits\Blamable;
use App\Traits\Currency;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';
    protected $fillable = [
        'req_recid',
        'req_email',
        'type',
        'category',
        'account_name',
        'account_number',
        'bank_name',
        'swift_code',
        'bank_address',
        'tel',
        'company',
        'id_no',
        'contact_no',
        'address',
        'ref',
        'req_date',
        'due_date',
    ];

    public function paymentBodies()
    {
        return $this->hasMany(Paymentbody::class, 'req_recid', 'req_recid');
    }
    public function getUserApprovalLevel()
    {
        $flows = $this->getCurrentFlowConfig();
        $levelApprovers = [];

        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        $query_from_user = User::where(['email' => $tasklist->req_email, 'group_id' => 'accounting'])->first();
        if (!$reviewApprover) {
            return [];
        }
        if (!$tasklist) {
            return [];
        }
        /**if the request is made from Accounting Team */
        $tasklistStepNumber = $tasklist->step_number;
        if(!empty($query_from_user) and $tasklistStepNumber > 4){
            $tasklistStepNumber = $tasklist->step_number -1;
        }
        if(!empty($query_from_user) and !$reviewApprover->review){
            $tasklistStepNumber = $tasklist->step_number -1;
        }
        foreach ($flows as $flow) {
            $isPending = false;
            if ($tasklistStepNumber == $flow->step_number) {
                $isPending = true;
                if($tasklist->next_checker_group == 1 or $tasklist->next_checker_group == 'close'){
                    $isPending = false;
                }
            }

            /** check if this form is assign back so it should pending on requester */
            if ($tasklist->isAssignedBack()) {
                $isPending = false;
            }

            if ($flow->checker == 'first_reviewer') {
                /** check if first reviewer has been skip */
                if ($reviewApprover->review) {
                    $user = User::firstWhere('email', $reviewApprover->review);
                    array_push($levelApprovers, [
                        'checker'    =>$flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Reviewer',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                } else {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Reviewer',
                        'full_name'  => "N/A",
                        'is_pending' => $isPending
                    ]);
                }
            }
            if ($flow->checker == 'second_reviewer') {
                /** check if second reviewer has been skip */
                if ($reviewApprover->second_review) {
                    $user = User::firstWhere('email', $reviewApprover->second_review);
                    array_push($levelApprovers, [
                        'checker'    =>$flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Second Reviewer',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                }
            }
            if ($flow->checker == 'third_reviewer') {
                /** check if third reviewer has been skip */
                if ($reviewApprover->third_review) {
                    $user = User::firstWhere('email', $reviewApprover->third_review);
                    array_push($levelApprovers, [
                        'checker'    =>$flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Third Reviewer',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                }
            }
            if ($flow->checker == 'fourth_reviewer') {
                /** check if fourth reviewer has been skip */
                if ($reviewApprover->fourth_reviewer) {
                    $user = User::firstWhere('email', $reviewApprover->fourth_reviewer);
                    array_push($levelApprovers, [
                        'checker'    =>$flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Fourth Reviewer',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                }
            }


            if ($flow->checker == 'accounting') {
                /** check if accounting member has already approve then display his or her name */
                $accountingName = $this->getAccountingReviewer();
                if ($accountingName) {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Accounting Review',
                        'full_name'  => $accountingName,
                        'is_pending' => $isPending
                    ]);
                } else {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Accounting Review',
                        'full_name'  => 'accounting',
                        'is_pending' => $isPending
                    ]);
                }
            }


            if ($flow->checker == 'accounting_finance') {
                /** check if accounting member has already approve then display his or her name */
                $accountingName = $this->getAccountingLastApproverName();
                if ($accountingName) {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Payment Process',
                        'full_name'  => $accountingName,
                        'is_pending' => $isPending
                    ]);
                } else {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step_number,
                        'label'      => 'Payment Process',
                        'full_name'  => 'Accounting & Finance',
                        'is_pending' => $isPending
                    ]);
                }
            }


            if ($flow->checker == 'approver') {
                $user = User::firstWhere('email', $reviewApprover->approve);
                array_push($levelApprovers, [
                    'checker'    => $flow->checker,
                    'step_number'=> $flow->step_number,
                    'label'      => 'Approver',
                    'full_name'  => "{$user->firstname} {$user->lastname}",
                    'is_pending' => $isPending
                ]);
            }


            if ($flow->checker == 'md_office') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_MDOFFICE')->first();
                array_push($levelApprovers, [
                    'checker'    => $flow->checker,
                    'step_number'=> $flow->step_number,
                    'label'      => 'MD Office',
                    'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                    'is_pending' => $isPending
                ]);
            }

            if ($flow->checker == 'approver_ceo') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CEO')->first();
                array_push($levelApprovers, [
                    'checker'    => $flow->checker,
                    'step_number'=> $flow->step_number,
                    'label'      => 'Approver (CEO)',
                    'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                    'is_pending' =>  $isPending
                ]);
            }

            if ($flow->checker == 'approver_cfo') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CFO')->where('groupid.is_cfo','1')->first();
                array_push($levelApprovers, [
                    'checker'    => $flow->checker,
                    'step_number'=> $flow->step_number,
                    'label'      => 'CFO',
                    'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                    'is_pending' => $isPending
                ]);
            }
        }

        $approvers = collect($levelApprovers)->transform(function ($approver) {
            return (object)$approver;
        });
        return $approvers;
    }
    public function getCurrentFlowConfig()
    {
        /** find is that request is within budget */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return null;
        }

        $withinBudget = $tasklist->within_budget;
        $totalAmountRequest = $this->total_amount_usd;

        /** determin condition in flow config */
        $flowAmountRequest = $this->getRequestAmountForFlowConfig($totalAmountRequest);

        /** check if current user is accounting team */
        $isAccountingTeam = false;
        $groupId = Groupid::where('email', $tasklist->req_email)->where('group_id', 'GROUP_ACCOUNTING')->first();
        if ($groupId) {
            $isAccountingTeam = true;
        }

        /**find flow config */
        $flowConfigs = Flowconfig::where('req_name', FormTypeEnum::AdvanceFormRequest())
                    ->where('within_budget', $withinBudget)
                    ->where('amount_request', $flowAmountRequest)
                    ->where('version', '2')
                    ->where('is_accounting_team', $isAccountingTeam)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->get();
        return  $flowConfigs;
    }
    public function getAccountingReviewer()
    {
        $audit = Auditlog::join('groupid', 'groupid.email', '=', 'auditlog.doer_email')
                ->where('auditlog.req_recid', $this->req_recid)
                ->where('groupid.group_id', 'GROUP_ACCOUNTING')
                ->first();
        if ($audit) {
            return $audit->doer_name;
        }

        return $audit;
    }
    public function getRequestAmountForFlowConfig($amount)
    {
        /** current we have only three condition only for flow config */
        if ($amount <= 10000) {
            return "<=10000";
        }

        if ($amount > 10000 && $amount <= 50000) {
            return "<=50000";
        }

        if ($amount > 50000) {
            return ">50000";
        }

        return "";
    }
    public function getAccountingLastApproverName()
    {
        /**find approved task list */
        $approvedTaskList = Tasklist::where('req_status', RequestStatusEnum::Approved())
                    ->where('req_recid', $this->req_recid)
                    ->first();
        if (!$approvedTaskList) {
            return null;
        }

        $audit = Auditlog::where('req_recid', $this->req_recid)
                ->orderBy('id', 'DESC')
                ->first();
        if ($audit) {
            return $audit->doer_name;
        }

        return $audit;
    }
}
