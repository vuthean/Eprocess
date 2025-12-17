<?php

namespace App\Models;

use App\Enums\FormTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tasklist extends Model
{
    use HasFactory;

    protected $table = 'tasklist';
    protected $fillable = [
        'req_recid',
        'req_email',
        'req_name',
        'req_branch',
        'req_position',
        'req_from',
        'req_type',
        'next_checker_group',
        'next_checker_role',
        'step_number',
        'step_status',
        'req_status',
        'req_date',
        'within_budget',
        'assign_back_by',
        'by_role',
        'by_step',
        'change_status_request_to',
        'is_new_flow'
    ];

    public function isApprovedCompleted()
    {
        return $this->req_status == '005';
    }

    public function isAssignedBack()
    {
        return $this->assign_back_by;
    }

    public function isQuery()
    {
        return $this->req_status == '006';
    }

    public function isWithinBudget()
    {
        return $this->within_budget == 'Y';
    }

    public function getAllApprovers()
    {
        $procurment = Procurement::firstWhere('req_recid', $this->req_recid);
        $total = $procurment->getTotalRequestAmount();
        $bid = $procurment->bid;
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        $currentStep = $this->step_number;

        if ($this->isWithinBudget()) {
            $isCEO = 0;
            $isDCEO = 0;
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
            // add DCEO
            if ($reviewApprover->hasApproverAsDCEO()) {
                $isDCEO = 1;
            }
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
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
            if ($bid == 'yes' and $isCEO == 0) {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', 'Y')
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->get();
            } else {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', null)
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->get();
            }
            $approvers = [];
            foreach ($flowConfigures as $flowConfig) {

                /** first reviewer */
                if ($flowConfig->checker == 'first_reviewer') {
                    if ($reviewApprover->review) {
                        $user = User::firstWhere('email', $reviewApprover->review);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'First reviewer',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    } else {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'First reviewer',
                            'full_name'  => "N/A",
                            'is_pending' => false
                        ]);
                    }
                }
                /** second reviewer */
                if ($flowConfig->checker == 'second_reviewer') {
                    if ($reviewApprover->second_review) {
                        $user = User::firstWhere('email', $reviewApprover->second_review);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Second reviewer',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    } else {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Second reviewer',
                            'full_name'  => "N/A",
                            'is_pending' => false
                        ]);
                    }
                }
                 /** Third reviewer */
                 if ($flowConfig->checker == 'third_reviewer') {
                    if ($reviewApprover->third_review) {
                        $user = User::firstWhere('email', $reviewApprover->third_review);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Third reviewer',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    } else {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Third reviewer',
                            'full_name'  => "N/A",
                            'is_pending' => false
                        ]);
                    }
                }
                 /** Forth reviewer */
                 if ($flowConfig->checker == 'forth_reviewer') {
                    if ($reviewApprover->fourth_reviewer) {
                        $user = User::firstWhere('email', $reviewApprover->fourth_reviewer);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Forth reviewer',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    } else {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Forth reviewer',
                            'full_name'  => "N/A",
                            'is_pending' => false
                        ]);
                    }
                }
                /** Accounting Team */
                if ($flowConfig->checker == 'accounting') {
                    if ($reviewApprover->accounting) {
                        $fullName = "Accounting";
                        if($currentStep > 5){
                            $accountingReview = Auditlog::where('req_recid', $this->req_recid)
                                                          ->where('doer_role','Accounting Reviewer')
                                                          ->first();
                            if($accountingReview){
                                $fullName = $accountingReview->doer_name;
                            }
                        }
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Accounting Review',
                                'full_name'  => $fullName,
                                'is_pending' => $flowConfig->checker == 'accounting' && $flowConfig->step_number == $this->step_number?true : false
                            ]);
                    }
                }
                /** Procurement Team */
                if ($flowConfig->checker == 'procurement') {
                    if ($reviewApprover->procurement) {
                        $user = User::firstWhere('email', $reviewApprover->procurement);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Procurement Review',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    }
                }
                /** budget owner */
                if ($flowConfig->checker == 'budget_owner') {
                    if ($reviewApprover->budget_owner) {
                        $user = User::firstWhere('email', $reviewApprover->budget_owner);
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Budget Owner',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    }
                }

                /**md_office */
                if ($flowConfig->checker == 'md_office') {
                    $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_MDOFFICE')->first();
                    if ($groupId) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'MD Office',
                            'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                            'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                }

                /**approver CEO*/
                if ($flowConfig->checker == 'approver_ceo') {
                    $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CEO')->first();
                    if ($groupId) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Approver (CEO)',
                            'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                            'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                }

                /**approver*/
                if ($flowConfig->checker == 'approver') {
                    $user = User::firstWhere('email', $reviewApprover->approve);
                    $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_DCEO')->where('groupid.email', $reviewApprover->approve)->where('groupid.status',1)->first();

                    if ($groupId) {
                        $groupIdDCEOOffice = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_DCEO_OFFICE')->where('groupid.status',1)->first();
                        if($groupIdDCEOOffice){
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'DCEO office',
                                'full_name'  => "{$groupIdDCEOOffice->firstname} {$groupIdDCEOOffice->lastname}",
                                'is_pending' => $groupIdDCEOOffice->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number+1 ? true : false
                            ]);
                        }
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number+1,
                            'label'      => 'Approver (DCEO)',
                            'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                            'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                        
                    }else{
                        if ($user) {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'      => 'Approver',
                                'full_name'  => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    }
                }

                /**approver cfo*/
                if ($flowConfig->checker == 'approver_cfo') {
                    $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CFO')->where('groupid.is_cfo', '1')->first();
                    if ($groupId) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'CFO',
                            'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                            'is_pending' => $groupId->email == $this->next_checker_group  && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                }

                /**Procure by*/
                if ($flowConfig->checker == 'receiver') {
                    $user = User::firstWhere('email', $reviewApprover->final);
                    if ($user) {
                        if ($bid == 'yes') {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number + 1,
                                'label'     => 'Receiver',
                                'full_name' => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        } else {
                            array_push($approvers, [
                                'step_number' => $flowConfig->step_number,
                                'label'     => 'Receiver',
                                'full_name' => "{$user->firstname} {$user->lastname}",
                                'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                            ]);
                        }
                    }
                }
                /**Co-approver*/
                if ($flowConfig->checker == 'co_approver') {
                    $user = User::firstWhere('email', $reviewApprover->co_approver);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Co Approver',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                }
            }
            $approver = collect($approvers)->transform(function ($approver) {
                return (object)$approver;
            });
            return $approver;
        }


        $approvers = [];
        $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
            ->where('within_budget', 'N')
            ->where('version', 2)
            ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
            ->get();
        foreach ($flowConfigures as $flowConfig) {
            /** first reviewer */
            if ($flowConfig->checker == 'first_reviewer') {
                if ($reviewApprover->review) {
                    $user = User::firstWhere('email', $reviewApprover->review);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'First reviewer',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                } else {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'First reviewer',
                        'full_name'  => "N/A",
                        'is_pending' => false
                    ]);
                }
            }

            /** second reviewer */
            if ($flowConfig->checker == 'second_reviewer') {
                if ($reviewApprover->second_review) {
                    $user = User::firstWhere('email', $reviewApprover->second_review);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Second reviewer',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                } else {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'Second reviewer',
                        'full_name'  => "N/A",
                        'is_pending' => false
                    ]);
                }
            }
             /** Third reviewer */
             if ($flowConfig->checker == 'third_reviewer') {
                if ($reviewApprover->third_review) {
                    $user = User::firstWhere('email', $reviewApprover->third_review);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Third reviewer',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                } else {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'Third reviewer',
                        'full_name'  => "N/A",
                        'is_pending' => false
                    ]);
                }
            }
             /** Forth reviewer */
             if ($flowConfig->checker == 'forth_reviewer') {
                if ($reviewApprover->fourth_reviewer) {
                    $user = User::firstWhere('email', $reviewApprover->fourth_reviewer);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Forth reviewer',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                } else {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'Forth reviewer',
                        'full_name'  => "N/A",
                        'is_pending' => false
                    ]);
                }
            }
             /** Accounting Team */
             if ($flowConfig->checker == 'accounting') {
                if ($reviewApprover->accounting) {
                    $fullName = "Accounting";
                    if($currentStep > 5){
                        $accountingReview = Auditlog::where('req_recid', $this->req_recid)
                                                      ->where('doer_role','Accounting Reviewer')
                                                      ->first();
                        if($accountingReview){
                            $fullName = $accountingReview->doer_name;
                        }
                    }
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Accounting Review',
                            'full_name'  => $fullName,
                            'is_pending' => $flowConfig->checker == 'accounting' && $flowConfig->step_number == $this->step_number?true : false
                        ]);
                }
            }
            /** Procurement Team */
            if ($flowConfig->checker == 'procurement') {
                if ($reviewApprover->procurement) {
                    $user = User::firstWhere('email', $reviewApprover->procurement);
                    if ($user) {
                        array_push($approvers, [
                            'step_number' => $flowConfig->step_number,
                            'label'      => 'Procurement Review',
                            'full_name'  => "{$user->firstname} {$user->lastname}",
                            'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                        ]);
                    }
                }
            }
            /**approver cfo*/
            if ($flowConfig->checker == 'approver_cfo') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CFO')->where('groupid.is_cfo', '1')->first();
                if ($groupId) {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'CFO',
                        'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                        'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                    ]);
                }
            }

            /**md_office */
            if ($flowConfig->checker == 'md_office') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_MDOFFICE')->first();
                if ($groupId) {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'MD Office',
                        'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                        'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                    ]);
                }
            }

            /**approver CEO*/
            if ($flowConfig->checker == 'approver_ceo') {
                $groupId = Groupid::join('users', 'users.email', '=', 'groupid.email')->where('groupid.group_id', 'GROUP_CEO')->first();
                if ($groupId) {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'Approver (CEO)',
                        'full_name'  => "{$groupId->firstname} {$groupId->lastname}",
                        'is_pending' => $groupId->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                    ]);
                }
            }

            /**Procure by*/
            if ($flowConfig->checker == 'receiver') {
                $user = User::firstWhere('email', $reviewApprover->final);
                if ($user) {
                    array_push($approvers, [
                        'step_number' => $flowConfig->step_number,
                        'label'      => 'Receiver',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $user->email == $this->next_checker_group && $flowConfig->step_number == $this->step_number ? true : false
                    ]);
                }
            }
        }
        $approver = collect($approvers)->transform(function ($approver) {
            return (object)$approver;
        });
        return $approver;
    }

    public function getPendingUser()
    {
        $user = User::firstWhere('email', $this->next_checker_group);
        if ($this->req_status != '005') {
            if ($this->next_checker_group == 'accounting_voucher' or $this->next_checker_group == 'accounting') {
                if ($this->step_number > 3) {
                    return 'Accounting & Finance';
                }
                return 'Accounting';
            }
            if (!$user) {
                return 'N/A';
            }
            return "{$user->firstname} {$user->lastname}";
        }else{
            if($this->change_status_request_to){
                return "{$user->firstname} {$user->lastname}";
            }
            return 'N/A';
        }
       
    }
    public function checkRole($task){
        $procurment = Procurement::firstWhere('req_recid', $task->req_recid);
        $total = $procurment->getTotalRequestAmount();
        $bid = $procurment->bid;
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $task->req_recid);
        if ($this->isWithinBudget()) {
            $isCEO = 0;
            $isDCEO = 0;
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
            // add DCEO
            if ($reviewApprover->hasApproverAsDCEO()) {
                $isDCEO = 1;
            }
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
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
            if ($bid == 'yes' and $isCEO == 0) {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('step_number',$task->step_number)
                    ->where('request_is_sole_source', 'Y')
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->first();
            } else {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', null)
                    ->where('step_number',$task->step_number)
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->first();
            }
            return $flowConfigures->step_description;
        }
        $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
            ->where('within_budget', 'N')
            ->where('version', 2)
            ->where('step_number',$task->step_number)
            ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
            ->first();

        return $flowConfigures->step_description;
    }
    public function checkAction($role){
        if($role == 'First Reviewer' or 
           $role == 'Second Reviewer' or
           $role == 'Third Reviewer' or
           $role == 'Fourth Reviewer' or
           $role == 'Accounting Reviewer' or 
           $role == 'Procurement Reviewer' ){
            return 'Reviewed Request';
        }elseif($role == 'Budget Owner'){
            return 'Approved on Budget code';
        }elseif($role == 'Approver'){
            return 'Approved request';
        }elseif($role == 'Procure by'){
            return 'Received Request';
        }elseif($role == 'MD office'){
            return 'Reviewed Request';
        }elseif($role == 'CEO'){
            return 'Approved request';
        }elseif($role == 'Co Approver'){
            return 'Approved request';
        }else{
            return 'Reviewed Request';
        }
    }
    public function checkRolePayment($task, $max_spent){
        $total = $max_spent;
        $bid = $procurment->bid;
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $task->req_recid);
        if ($this->isWithinBudget()) {
            $isCEO = 0;
            $isDCEO = 0;
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
            // add DCEO
            if ($reviewApprover->hasApproverAsDCEO()) {
                $isDCEO = 1;
            }
            if ($reviewApprover->hasApproverAsCEO()) {
                $isCEO = 1;
            }
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
            if ($bid == 'yes' and $isCEO == 0) {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('step_number',$task->step_number)
                    ->where('request_is_sole_source', 'Y')
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->first();
            } else {
                $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
                    ->where('within_budget', 'Y')
                    ->where('amount_request', $amountRequest)
                    ->where('approver_is_ceo', $isCEO)
                    ->where('request_is_sole_source', null)
                    ->where('step_number',$task->step_number)
                    ->where('version', 2)
                    ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
                    ->first();
            }
            return $flowConfigures->step_description;
        }
        $flowConfigures = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
            ->where('within_budget', 'N')
            ->where('version', 2)
            ->where('step_number',$task->step_number)
            ->orderBy(DB::raw('CAST(step_number AS UNSIGNED)'), 'asc')
            ->first();

        return $flowConfigures->step_description;
    }
    public function checkActionPayment($role){
        if($role == 'Reviewer'  ){
            return 'Reviewed';
        }elseif($role == 'Approver'){
            return 'Approved ';
        }elseif($role == 'Accounting Reviewer'){
            return 'Accounting Reviewed';
        }elseif($role == 'Payment Process'){
            return 'Payment confirmed';
        }else{
            return 'Reviewed';
        }
    }
}
