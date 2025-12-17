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

class CashReceiptVoucher extends Model
{
    use HasFactory;
    use Blamable;
    use Currency;
    protected $table    = 'cash_receipt_vouchers';

    protected $fillable = [
        'req_recid',
        'voucher_number',
        'batch_number',
        'branch',
        'department',
        'request_date',
        'exported_at',
        'currency',
        'ref_no',
        'payment_method_code',
        'payment_method_email_content',
        'payment_method_group_id',
        'is_sent_email',
        'is_sent_email_failed',
        'bank_name',
        'account_name',
        'account_number',
        'account_currency',
        'swift_code',
        'beneficiary_number',
        'invoice_number',
        'note',
        'summary_budgets',
        'exchange_rate',
        'total_for_approval_usd',
        'created_at',
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

            $localCurrencyAmount = $this->getLCYAmount($exchangeRate, $item->currencies[$i], $item->amounts[$i]);

            CashReceiptVoucherDetail::create([
                'req_recid' => $this->req_recid,
                'gl_code'=>$glCode->account_number,
                'account_name'=>$glCode->account_name,
                'branch_code'=>$item->branch_codes[$i],
                'currency' => $item->currencies[$i],
                'dr_cr' => $item->dr_crs[$i],
                'amount'=> $item->amounts[$i],
                'lcy_amount' => $localCurrencyAmount,
                'budget_code'=>$item->budget_codes[$i],
                'al_budget_code'=>$item->al_budget_codes[$i],
                'tax_code'=>$item->tax_codes[$i],
                'supp_code'=>$item->supp_codes[$i],
                'department_code'=>$item->dept_codes[$i],
                'product_code'=>$item->pro_codes[$i],
                'segment_code'=>$item->seg_codes[$i],
                'naratives'=>$item->naratives[$i],
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
        $cashReceiptDetails = CashReceiptVoucherDetail::where('req_recid', $this->req_recid)
                        ->where('dr_cr', 'DEBIT')
                        ->get();

        $totalDebitAmountUSD = 0;

        /** find USD amount */
        $cashReceiptDetailUSDs = collect($cashReceiptDetails)->where('currency', 'USD');
        if (collect($cashReceiptDetailUSDs)->isNotEmpty()) {
            $totalCashReceiptkDetialAmountUSD = collect($cashReceiptDetailUSDs)->sum('amount');
            $totalDebitAmountUSD = $totalCashReceiptkDetialAmountUSD;
        }

        /** find KHR amount */
        $cashReceiptDetailKHR = collect($cashReceiptDetails)->where('currency', 'KHR');
        if (collect($cashReceiptDetailKHR)->isNotEmpty()) {
            $totalReceiptDetialAmountKHR = collect($cashReceiptDetailKHR)->sum('amount');
            $systemExhcangeRate = $this->currentExchangeRate();

            $totalUSD = $totalReceiptDetialAmountKHR / $systemExhcangeRate;
            $totalDebitAmountUSD += $totalUSD;
        }
        CashReceiptVoucher::where('req_recid', $this->req_recid)->update([
            'total_for_approval_usd' => $totalDebitAmountUSD,
        ]);
    }
    public function blockAllReference()
    {
        $references = $this->ref_no;
        $requestIds = explode(',', $references);
        if (count($requestIds) <= 0) {
            return null;
        }

        DB::transaction(function () use ($requestIds) {
            /** block payment body */
            Paymentbody::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>$this->req_recid]);

            /** block procurement body */
            Procurementbody::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>$this->req_recid]);

            /** block advance request detail */
            AdvanceFormDetail::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>$this->req_recid]);

            /** block clear advance form request detail */
            ClearAdvanceFormDetail::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>$this->req_recid]);
        });
    }
    public function getTotalDRCR()
    {
        $totalDebitAmount = 0;
        $totalCreditAmount = 0;

        /** find bank details */
        $cashReceiptDetails = CashReceiptVoucherDetail::where('req_recid', $this->req_recid)->get();
        $currencies = collect($cashReceiptDetails)->pluck('currency');

        $isCrossCurrency = $this->isCrossCurrency($currencies);
        if ($isCrossCurrency) {
            $exchangeRate = $this->exchange_rate;
            foreach ($cashReceiptDetails as $cashReceiptDetail) {
                if ($cashReceiptDetail->dr_cr == 'DEBIT') {
                    $debitAmountUSD = $cashReceiptDetail->amount;
                    if ($cashReceiptDetail->currency == 'KHR') {
                        $debitAmountUSD = $cashReceiptDetail->amount / $exchangeRate;
                    }
                    $totalDebitAmount += $debitAmountUSD;
                }
                if ($cashReceiptDetail->dr_cr == 'CREDIT') {
                    $creditAmountUSD = $cashReceiptDetail->amount;
                    if ($cashReceiptDetail->currency == 'KHR') {
                        $creditAmountUSD = $cashReceiptDetail->amount / $exchangeRate;
                    }
                    $totalCreditAmount += $creditAmountUSD;
                }
            }
        } else {
            foreach ($cashReceiptDetails as $cashReceiptDetail) {
                if ($cashReceiptDetail->dr_cr == 'DEBIT') {
                    $debitAmountUSD = $cashReceiptDetail->amount;
                    $totalDebitAmount += $debitAmountUSD;
                }
                if ($cashReceiptDetail->dr_cr == 'CREDIT') {
                    $creditAmountUSD = $cashReceiptDetail->amount;
                    $totalCreditAmount += $creditAmountUSD;
                }
            }
        }
        return (object)([
            'total_DR'=> round($totalDebitAmount, 2),
            'total_CR'=>round($totalCreditAmount, 2),
        ]);
    }
    public function getAllApprovers()
    {
        $totalDebitAmount = $this->total_for_approval_usd;
        $flows = $this->getFlowConfigByAmount($totalDebitAmount);
        if (collect($flows)->isEmpty()) {
            return [];
        }

        $stepAmount  = collect($flows)->pluck('step');
        $uniqueSteps = collect($stepAmount)->unique()->sort();

        $approverLevels = [];
        foreach ($uniqueSteps as $step) {
            $flowConfig = collect($flows)->firstWhere('step', $step);
            $groupIds   = collect($flows)->pluck('group_id');
            $users      = $this->getAllUserByGroup($groupIds);
            $defaultUserEmail = $this->getDefaultApproverEmail($totalDebitAmount);

            array_push($approverLevels, [
                'checker'      => $flowConfig->checker ,
                'step_number'  => $step,
                'label'        => $flowConfig->checker == 'accounting_voucher' ? 'Accounting & Finance' : $flowConfig->checker,
                'users'        => $users,
                'default_user_email' => $defaultUserEmail,
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
                ->whereIn('groupid.group_id',['GROUP_ACCOUNTING','GROUP_CFO'])
                ->where('groupid.status',1)
                ->get();
        return $users;
    }
    private function getDefaultApproverEmail($totalDebitAmount)
    {
        /**get all email for default approve */
       $emails = new EmailDefaultApprove();
    //    /** this case is just want to set default approver level only */
       if ($totalDebitAmount <= 500) { 
           $default_email =  $emails->where('level',1)->first();
           return $default_email->email;
       }

       if ($totalDebitAmount <= 2500) {
           $default_email =  $emails->where('level',2)->first();
           return $default_email->email;
       }

       if ($totalDebitAmount <= 5000) {
           $default_email =  $emails->where('level',3)->first();
           return $default_email->email;
       }

       if ($totalDebitAmount > 5000) {
           $default_email =  $emails->where('level',4)->first();
           return $default_email->email;
       }
    }
    public function isCrossCurrency($currencies)
    {
        $currency = collect($currencies)->unique();
        if (collect($currency)->count() == 1) {
            return false;
        }
        return true;
    }
    public function getFlowConfigByAmount($amount)
    {
        /** find min amount */
        $actualFlow = CashReceiptVoucherFlowconfig::where('min_amount', '<', $amount)->orderBy('min_amount', 'DESC')->first();
        if (!$actualFlow) {
            return [];
        }

        /** get all flows */
        $flowConfigs = CashReceiptVoucherFlowconfig::where('min_amount', $actualFlow->min_amount)->get();
        return $flowConfigs;
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
            'final'     => 'accounting_voucher',
        ]);

        /** alert to pending user */
        Tasklist::where('req_recid', $req_recid)->update([
            'next_checker_group' => $reviewer_email,
            'next_checker_role'  => $reviewer_role,
            'step_number'        => 1,
            'req_status'         => '002'
        ]);
    }
    public function saveLog($comment)
    {
        $user = Auth::user();
        Auditlog::create([
            'req_recid'     =>$this->req_recid,
            'doer_email'    => $user->email,
            'doer_name'     => "{$user->firstname} {$user->lastname}",
            'doer_branch'   => $user->department,
            'doer_position' => $user->position,
            'activity_code' => ActivityCodeEnum::Submitted(),
            'activity_description' => $comment,
            'activity_form'        => FormTypeEnum::CashReceiptVourcherRequest(),
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
            $subject      = 'Cash Receipt Voucher form request',
            $checker      = $tasklist->next_checker_group,
            $cc           = $tasklist->req_email,
            $comment      = $comment
        );
        return $result;
    }
    public function refreshReference()
    {
        if ($this->ref_no) {
            $requestIds = explode(',', $this->ref_no);
            if (count($requestIds) > 0) {
                DB::transaction(function () use ($requestIds) {
                    /** clear payment body */
                    Paymentbody::whereNotIn('req_recid', $requestIds)->where('used_by_request_cash_receipt', $this->req_recid)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear procurment body */
                    Procurementbody::whereNotIn('req_recid', $requestIds)->where('used_by_request_cash_receipt', $this->req_recid)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear advance detail */
                    AdvanceFormDetail::whereNotIn('req_recid', $requestIds)->where('used_by_request_cash_receipt', $this->req_recid)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear clear advan detail */
                    ClearAdvanceFormDetail::whereNotIn('req_recid', $requestIds)->where('used_by_request_cash_receipt', $this->req_recid)->update(['used_by_request_cash_receipt'=>null]);
                });
            }
        }
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

            if ($flow->checker == 'accounting_voucher') {
                $accountingName = $this->getAccountingReviewer();
                if ($accountingName) {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step,
                        'label'      => 'Accounting ',
                        'full_name'  => 'accounting',
                        'is_pending' => $isPending
                    ]);
                } else {
                    array_push($levelApprovers, [
                        'checker'    => $flow->checker,
                        'step_number'=> $flow->step,
                        'label'      => 'Accounting',
                        'full_name'  => 'accounting',
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
    public function findPaidBy()
    {
        //** all paid is based on accounting team */
        $tasklist = Tasklist::where('req_recid', $this->req_recid)->where('req_status', RequestStatusEnum::Approved())->first();
        if (!$tasklist) {
            return $tasklist;
        }
        $auditLog = Auditlog::join('users', 'users.email', '=', 'auditlog.doer_email')
                    ->select('users.fullname', 'users.position', 'auditlog.created_at')
                    ->where('auditlog.req_recid', $this->req_recid)
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

        if ($tasklist->next_checker_group == 'accounting_voucher') {
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
    public function freeReferences()
    {
        if ($this->ref_no) {
            $requestIds = explode(',', $this->ref_no);
            if (count($requestIds) > 0) {
                DB::transaction(function () use ($requestIds) {
                    /** clear payment body */
                    Paymentbody::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear procurment body */
                    Procurementbody::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear advance detail */
                    AdvanceFormDetail::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>null]);
                    /** clear clear advan detail */
                    ClearAdvanceFormDetail::whereIn('req_recid', $requestIds)->update(['used_by_request_cash_receipt'=>null]);
                });
            }
        }
    }
    public function sendEmailToPaymentMethodFor($items)
    {
        $item = (object)$items;

        /**@var Tasklist $tasklist */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        $paymentMethodCode = $item->payment_method_code;
        $emailConten = $item->payment_method_email_content;


        /** find payment method detail */
        $paymentMethod = PaymentMethod::firstWhere('name', $paymentMethodCode);

        if ($paymentMethod) {
            $paymentMethodDetials = PaymentMethodDetail::where('payment_method_id', $paymentMethod->id)->get();
            $groupIds = collect($paymentMethodDetials)->pluck('group_id');
            $sendingGroupIds = '';
            $isFailed = false;
            if (collect($groupIds)->isNotEmpty()) {
                $groups = Groupid::whereIn('group_id', $groupIds)->where('status',1)->get();
                foreach ($groups as $group) {
                    $emailService = new Sendemail();
                    $result = $emailService->sendEmail(
                        $content      = $emailConten,
                        $rec_id       = $this->req_recid,
                        $req_name     = $tasklist->req_name,
                        $req_branch   = $tasklist->req_branch,
                        $req_position = $tasklist->req_position,
                        $subject      = 'Cash Receipt Voucher',
                        $checker      = $group->email,
                        $cc           = '',
                        $comment      = ''
                    );
                    if ($result == 'fail') {
                        $isFailed = true;
                    }
                }

                /** update form after send */
                $sendingGroupIds = collect($groupIds)->implode(',');
            }
            CashReceiptVoucher::where('req_recid', $this->req_recid)->update([
                'payment_method_code'=>$paymentMethod->name,
                'payment_method_email_content'=>$emailConten,
                'payment_method_group_id'=>$sendingGroupIds,
                'is_sent_email' => true,
                'is_sent_email_failed' => $isFailed,
            ]);
        }
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
                $subject      = 'Cash Receipt Voucher form request',
                $checker      = $tasklist->req_email,
                $cc           = '',
                $comment      = $comment
            );
            return $result;
        };
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
            $subject      = 'Cash Receipt Voucher form request',
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
            $subject      = 'Cash Receipt Voucher form request',
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
            $subject      = 'Cash Receipt Voucher form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment
        );
        return $result;
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
