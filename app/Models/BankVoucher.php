<?php

namespace App\Models;

use App\Enums\ActivityCodeEnum;
use App\Enums\FormTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Myclass\Sendemail;
use App\Traits\Blamable;
use App\Traits\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\ErrorHandler\Collecting;

class BankVoucher extends Model
{
    use HasFactory;
    use Blamable;
    use Currency;
    protected $table    = 'bank_vouchers';

    protected $fillable = [
        'req_recid',
        'voucher_date',
        'voucher_number',
        'batch_number',
        'department',
        'request_date',
        'exported_at',
        'currency',
        'description',
        'note',
        'exchange_rate',
        'thb_exchange_rate',
        'eur_exchange_rate',
        'total_for_approval_usd',
    ];
    public function createDetails($details)
    {
        $item = (object)$details;
        $itemNmber = count($item->gl_codes);
        $exchangeRate = $item->exchange_rate;

        for ($i = 0; $i < $itemNmber; $i++) {
            $glCode = GeneralLedgerCode::firstOrCreate([
                'account_number'=>$item->gl_codes[$i]
            ], [
                'account_number'=>$item->gl_codes[$i],
                'account_name'=>"Generated from System",
            ]);
            if($item->currencies[$i] == 'KHR'){
                $exchangeRate = $item->exchange_rate;
            }
            if($item->currencies[$i] == 'THB'){
                $exchangeRate = $item->thb_exchange_rate;
            }
            if($item->currencies[$i] == 'EUR'){
                $exchangeRate = $item->eur_exchange_rate;
            }

            $localCurrencyAmount = $this->getLCYAmount($exchangeRate, $item->currencies[$i], $item->amounts[$i]);

            BankVourcherDetail::create([
                'req_recid' => $this->req_recid,
                'gl_code'=>$glCode->account_number,
                'account_name'=>$glCode->account_name,
                'branch_code'=>$item->branch_codes[$i],
                'currency' => $item->currencies[$i],
                'dr_cr' => $item->dr_crs[$i],
                'budget_code'=>$item->budget_codes[$i],
                'amount'=> $item->amounts[$i],
                'lcy_amount' => $localCurrencyAmount,
                'supp_code'=>$item->supp_codes[$i],
                'department_code'=>$item->dept_codes[$i],
                'descriptions'        =>  $item->descriptions[$i],
                
            ]);
        }
    }
    private function getLCYAmount($exchangeRate, $currency, $amount)
    {
        if ($currency == 'USD') {
            return $amount;
        }

        return $amount/ $exchangeRate;
    }
    public function updateTotalAmount()
    {
        /** find detail */
        $bankDetails = BankVourcherDetail::where('req_recid', $this->req_recid)
                        ->where('dr_cr', 'DEBIT')
                        ->get();
        $totalDebitAmountUSD = 0;

        /** find USD amount */
        $bankDetailUSDs = collect($bankDetails)->where('currency', 'USD');
        if (collect($bankDetailUSDs)->isNotEmpty()) {
            $totalBankDetialAmountUSD = collect($bankDetailUSDs)->sum('amount');
            $totalDebitAmountUSD = $totalBankDetialAmountUSD;
        }

        /** find KHR amount */
        $bankDetailKHR = collect($bankDetails)->where('currency', 'KHR');
        if (collect($bankDetailKHR)->isNotEmpty()) {
            $totalBankDetialAmountKHR = collect($bankDetailKHR)->sum('amount');
            $systemExhcangeRate = $this->exchange_rate;

            $totalUSD = $totalBankDetialAmountKHR / $systemExhcangeRate;
            $totalDebitAmountUSD += $totalUSD;
        }
         /** find THB amount */
         $bankDetailTHB = collect($bankDetails)->where('currency', 'THB');
         if (collect($bankDetailTHB)->isNotEmpty()) {
             $totalBankDetialAmountTHB = collect($bankDetailTHB)->sum('amount');
             $systemExhcangeRate = $this->thb_exchange_rate;
 
             $totalUSD = $totalBankDetialAmountTHB / $systemExhcangeRate;
             $totalDebitAmountUSD += $totalUSD;
         }
          /** find EUR amount */
        $bankDetailEUR = collect($bankDetails)->where('currency', 'EUR');
        if (collect($bankDetailEUR)->isNotEmpty()) {
            $totalBankDetialAmountEUR = collect($bankDetailEUR)->sum('amount');
            $systemExhcangeRate = $this->eur_exchange_rate;

            $totalUSD = $totalBankDetialAmountEUR / $systemExhcangeRate;
            $totalDebitAmountUSD += $totalUSD;
        }
        BankVoucher::where('req_recid', $this->req_recid)->update([
            'total_for_approval_usd' => $totalDebitAmountUSD,
        ]);
    }
    public function getTotalDRCR()
    {
        $totalDebitAmount = 0;
        $totalDebitAmountKHR = 0;
        $totalDebitAmountUSD = 0;
        $totalDebitAmountEUR = 0;
        $totalDebitAmountTHB = 0;
        $totalCreditAmount = 0;
        $totalCreditAmountKHR = 0;
        $totalCreditAmountUSD = 0;
        $totalCreditAmountEUR = 0;
        $totalCreditAmountTHB = 0;

        /** find bank details */
        $bankDetails = BankVourcherDetail::where('req_recid', $this->req_recid)->get();
        $currencies = collect($bankDetails)->pluck('currency');

        $isCrossCurrency = $this->isCrossCurrency($currencies);
        if ($isCrossCurrency) {
            $exchangeRate = $this->exchange_rate;
            $exchangeRateTHB = $this->thb_exchange_rate;
            $exchangeRateEUR = $this->eur_exchange_rate;
            foreach ($bankDetails as $bankDetail) {
                if ($bankDetail->dr_cr == 'DEBIT') {
                    $debitAmountUSD = $bankDetail->amount;
                    if ($bankDetail->currency == 'KHR') {
                        $totalDebitAmountKHR += $debitAmountUSD;
                        $debitAmountUSD = $bankDetail->amount / $exchangeRate;
                    }elseif($bankDetail->currency == 'THB'){
                        $totalDebitAmountTHB += $debitAmountUSD;
                        $debitAmountUSD = $bankDetail->amount / $exchangeRateTHB;
                    }elseif($bankDetail->currency == 'EUR'){
                        $totalDebitAmountEUR += $debitAmountUSD;
                        $debitAmountUSD = $bankDetail->amount / $exchangeRateEUR;
                    }else{
                        $totalDebitAmountUSD +=$debitAmountUSD;
                    }
                    $totalDebitAmount += $debitAmountUSD;
                    
                }
                if ($bankDetail->dr_cr == 'CREDIT') {
                    $creditAmountUSD = $bankDetail->amount;
                    if ($bankDetail->currency == 'KHR') {
                        $totalCreditAmountKHR += $creditAmountUSD;
                        $creditAmountUSD = $bankDetail->amount / $exchangeRate;
                    }elseif($bankDetail->currency == 'THB'){
                        $totalCreditAmountTHB += $creditAmountUSD;
                        $creditAmountUSD = $bankDetail->amount / $exchangeRateTHB;
                    }elseif($bankDetail->currency == 'EUR'){
                        $totalCreditAmountEUR += $creditAmountUSD;
                        $creditAmountUSD = $bankDetail->amount / $exchangeRateEUR;
                    }else{
                        $totalCreditAmountUSD += $creditAmountUSD;
                    }
                    $totalCreditAmount += $creditAmountUSD;
                }
            }
        } else {
            foreach ($bankDetails as $bankDetail) {
                if ($bankDetail->dr_cr == 'DEBIT') {
                    $debitAmountUSD = $bankDetail->amount;
                    $totalDebitAmount += $debitAmountUSD;
                    if ($bankDetail->currency == 'USD') {
                        $totalDebitAmountUSD =$totalDebitAmount;
                    }elseif($bankDetail->currency == 'THB'){
                        $totalDebitAmountTHB =$totalDebitAmount;
                    }elseif($bankDetail->currency == 'EUR'){
                        $totalDebitAmountEUR =$totalDebitAmount;
                    }else{
                        $totalDebitAmountKHR =$totalDebitAmount;
                    }
                }
                if ($bankDetail->dr_cr == 'CREDIT') {
                    $creditAmountUSD = $bankDetail->amount;
                    $totalCreditAmount += $creditAmountUSD;
                    if ($bankDetail->currency == 'USD') {
                        $totalCreditAmountUSD =$totalCreditAmount;
                    }elseif($bankDetail->currency == 'THB'){
                        $totalCreditAmountTHB =$totalCreditAmount;
                    }elseif($bankDetail->currency == 'EUR'){
                        $totalCreditAmountEUR =$totalCreditAmount;
                    }else{
                        $totalCreditAmountKHR =$totalCreditAmount;
                    }
                }
            }

        }
        return (object)([
            'total_DR'=> round($totalDebitAmount, 2),
            'total_CR'=>round($totalCreditAmount, 2),
            'total_DR_KHR'=> round($totalDebitAmountKHR, 2),
            'total_DR_USD'=> round($totalDebitAmountUSD, 2),
            'total_CR_KHR'=>round($totalCreditAmountKHR, 2),
            'total_CR_USD'=>round($totalCreditAmountUSD, 2),
            'total_DR_THB'=> round($totalDebitAmountTHB, 2),
            'total_DR_EUR'=> round($totalDebitAmountEUR, 2),
            'total_CR_THB'=>round($totalCreditAmountTHB, 2),
            'total_CR_EUR'=>round($totalCreditAmountEUR, 2),
        ]);
    }
    public function getAllApprovers()
    {
        $totalDebitAmount = $this->total_for_approval_usd;
        $flows = $this->getFlowConfigByAmount($totalDebitAmount);
        if (collect($flows)->isEmpty()) {
            return [];
        }
        $group = Groupid::where('group_id','GROUP_TREASURY')->where('status',1)->get();
        $stepAmount  = collect($flows)->pluck('step');
        $uniqueSteps = collect($stepAmount)->unique()->sort();
        $approverLevels=[];
        foreach ($uniqueSteps as $step) {
            $flowConfig = collect($flows)->firstWhere('step', $step);
            $groupIds   = collect($group)->pluck('group_id');
            $users      = $this->getAllUserByGroup($groupIds);

            array_push($approverLevels, [
                'checker'      => $flowConfig->checker ,
                'step_number'  => $step,
                'label'        => $flowConfig->checker,
                'users'        => $users,
            ]);
        }
        $approverLevels = collect($approverLevels)->transform(function ($approver) {
            return (object)$approver;
        });
        
        return $approverLevels;
    }
    private function getAllUserByGroup($groupIds)
    {
        $users = Groupid::join('usermgt', 'groupid.email', '=', 'usermgt.email')
                ->select('groupid.email', 'groupid.role_id', 'groupid.group_id', 'usermgt.firstname', 'usermgt.lastname')
                ->whereIn('groupid.group_id', $groupIds)
                ->where('groupid.role_id','!=','1')
                ->where('groupid.status',1)
                ->get();
        return $users;
    }
    public function getFlowConfigByAmount($amount)
    {
        /** find min amount */
        $actualFlow = BankVoucherFlowConfig::where('min_amount', '<', $amount)->orderBy('min_amount', 'DESC')->first();
        if (!$actualFlow) {
            return [];
        }

        /** get all flows */
        $flowConfigs = BankVoucherFlowConfig::where('min_amount', $actualFlow->min_amount)->get();
        return $flowConfigs;
    }
    
    public function isCrossCurrency($currencies)
    {
        $currency = collect($currencies)->unique();
        if (collect($currency)->count() == 1) {
            return false;
        }
        return true;
    }
    public function isAlreadySubmitted()
    {
        /**find task list */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if ($tasklist->req_status == RequestStatusEnum::Save()) {
            return false;
        }

        return true;
    }
    public function saveApprovalLevel($req_recid, $firstApprover, $approver)
    {
        $reviewers = explode('/', $firstApprover);
        $reviewer_email = $reviewers[0];
        $reviewer_role  = $reviewers[1];

        $approvers = explode('/', $approver);
        $approver_email = $approvers[0];

        Reviewapprove::create([
            'req_recid' => $req_recid,
            'review'    => $reviewer_email,
            'approve'   => $approver_email,
        ]);

        /** alert to pending user */
        Tasklist::where('req_recid', $req_recid)->update([
            'next_checker_group' => $reviewer_email,
            'next_checker_role'  => $reviewer_role,
            'step_number'        => 1,
            'req_status'         => '002'
        ]);
    }
    public function saveLog()
    {
        $user = Auth::user();
        Auditlog::create([
            'req_recid'     =>$this->req_recid,
            'doer_email'    => $user->email,
            'doer_name'     => "{$user->firstname} {$user->lastname}",
            'doer_branch'   => $user->department,
            'doer_position' => $user->position,
            'activity_code' => ActivityCodeEnum::Submitted(),
            'activity_description' => 'Submit request',
            'activity_form'        => FormTypeEnum::BankVourcherRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString()
        ]);
    }
    public function sendEmailToPendingUser($comment)
    {
        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmail(
            $content      = 'You have one request pending on your approval, Please check.',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Treasury Voucher form request',
            $checker      = $tasklist->next_checker_group,
            $cc           = $tasklist->req_email,
            $comment      = $comment
        );
        return $result;
    }
    public function getUserApprovalLevel()
    {
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return [];
        }

        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        if (!$reviewApprover) {
            return [];
        }

        $flowConfigs = $this->getFlowConfigByAmount($this->total_for_approval_usd);

        $levelApprovers = [];
        $flows = collect($flowConfigs)->unique('step');
        foreach ($flows as $flow) {
            $isPending = false;
            if ($tasklist->step_number == $flow->step) {
                $isPending = true;
            }

            /** check if this form is assign back so it should pending on requester */
            if ($tasklist->isAssignedBack()) {
                $isPending = false;
            }

            /** reviewer */
            if ($flow->checker == 'first_reviewer') {
                $user = User::firstWhere('email', $reviewApprover->review);
                if ($user) {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step,
                        'label'      => $flow->checker,
                        'full_name'  =>  "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                }
            }

            if ($flow->checker == 'approver') {
                $user = User::firstWhere('email', $reviewApprover->approve);
                if ($user) {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step,
                        'label'      => 'Approver',
                        'full_name'  => "{$user->firstname} {$user->lastname}",
                        'is_pending' => $isPending
                    ]);
                }
            }
        }

        $approvers = collect($levelApprovers)->transform(function ($approver) {
            return (object)$approver;
        });

        return $approvers;
    }
    public function findPreparedByUser()
    {
        $auditLog = Auditlog::join('users', 'users.email', '=', 'auditlog.doer_email')
                    ->select('users.fullname', 'users.position', 'auditlog.created_at')
                    ->where('auditlog.req_recid', $this->req_recid)
                    ->where('auditlog.activity_code', ActivityCodeEnum::Submitted())
                    ->first();
        return $auditLog;
    }
    public function findFirstReviewer()
    {
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        if (!$reviewApprover) {
            return $reviewApprover ;
        }

        $reviewerEmail = $reviewApprover->review;
        $auditLog = Auditlog::join('users', 'users.email', '=', 'auditlog.doer_email')
                    ->select('users.fullname', 'users.position', 'auditlog.created_at')
                    ->where('auditlog.req_recid', $this->req_recid)
                    ->where('auditlog.activity_code', ActivityCodeEnum::Approved())
                    ->where('auditlog.doer_email', $reviewerEmail)
                    ->orderBy('auditlog.id', 'DESC')
                    ->first();
        return $auditLog;
    }
    public function findApprover()
    {
        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        if (!$reviewApprover) {
            return $reviewApprover ;
        }

        $reviewerEmail = $reviewApprover->approve;
        $auditLog = Auditlog::join('users', 'users.email', '=', 'auditlog.doer_email')
                    ->select('users.fullname', 'users.position', 'auditlog.created_at')
                    ->where('auditlog.req_recid', $this->req_recid)
                    ->where('auditlog.activity_code', ActivityCodeEnum::Approved())
                    ->where('auditlog.doer_email', $reviewerEmail)
                    ->orderBy('auditlog.id', 'DESC')
                    ->first();
        return $auditLog;
    }
    public function isPendingOnUser($user)
    {
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return false;
        }

        if ($tasklist->next_checker_group == 'accounting') {
            $groupId = Groupid::where('email', $user->email)->where('group_id', 'GROUP_ACCOUNTING')->where('status',1)->first();
            if ($groupId) {
                return true;
            }
            return false;
        }

        if ($tasklist->next_checker_group == $user->email) {
            return true;
        }
        return false;
    }
    public function sendEmailFormHasBeenApproved($comment)
    {
        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to requester when form has been approved completed */
        if ($tasklist->isApprovedCompleted()) {
            $emailService = new Sendemail();
            $result = $emailService->sendEmail(
                $content      = 'Your request has been approved completed',
                $rec_id       = $this->req_recid,
                $req_name     = $tasklist->req_name,
                $req_branch   = $tasklist->req_branch,
                $req_position = $tasklist->req_position,
                $subject      = 'Treasury Voucher form request',
                $checker      = $tasklist->req_email,
                $cc           = '',
                $comment      = $comment
            );
            return $result;
        };

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmail(
            $content      = 'Your request has been approved completed',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Treasury Voucher form request',
            $checker      = $tasklist->next_checker_group,
            $cc           = $tasklist->req_email,
            $comment      = $comment
        );
        return $result;
    }
    public function sendEmailFormHasBeenRejected($comment)
    {
        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmail(
            $content      = 'Your request has been rejected',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Treasury Voucher form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment
        );
        return $result;
    }
    public function sendEmailFormHasBeenAssignedBack($comment)
    {
        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmail(
            $content      = 'Your request has been assigned back, Please check.',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Treasury Voucher form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment
        );
        return $result;
    }
    public function sendEmailFormHasBeenQueriedBack($comment)
    {
        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmail(
            $content      = 'Your request has been queried back, Please check.',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Treasury Voucher form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment
        );
        return $result;
    }
}
