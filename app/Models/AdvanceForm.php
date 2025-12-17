<?php

namespace App\Models;

use App\Enums\ActivityCodeEnum;
use App\Enums\BudgetEnum;
use App\Enums\ActionEnum;
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


class AdvanceForm extends Model
{
    use HasFactory;
    use Blamable;
    use Currency;

    protected $fillable = [
        'req_recid',
        'currency',
        'department',
        'request_date',
        'due_date',
        'category',
        'account_name',
        'account_number',
        'bank_name',
        'bank_address',
        'phone_number',
        'company_name',
        'id_number',
        'contact_number',
        'address',
        'additional_remark',
        'additional_remark_product_segment',
        'total_amount_usd',
        'total_amount_khr',
        'subject',
        'ref',
    ];

    protected $casts = [
        'request_date' => 'date',
        'due_date'     => 'date',
    ];

    public function isPendingOnUser(User $user)
    {
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return false;
        }

        if ($tasklist->next_checker_group == 'accounting') {
            $groupId = Groupid::where('email', $user->email)->where('group_id', 'GROUP_ACCOUNTING')->first();
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

    public function updateWithinBudgetForTasklist()
    {
        /**check if found one not within budget in detail, the we conculstion that this request is not within budget */
        $isNotWithinBudget = AdvanceFormDetail::where('req_recid', $this->req_recid)
                            ->where('within_budget', 'N')
                            ->first();
        if ($isNotWithinBudget) {
            Tasklist::where('req_recid', $this->req_recid)->update(['within_budget' => BudgetEnum::NotWithinBudget()]);
        } else {
            Tasklist::where('req_recid', $this->req_recid)->update(['within_budget' => BudgetEnum::WithinBudget()]);
        }
    }
    public function createDetail($detail)
    {
        $item  = (object)$detail;

        $itemNmber = count($item->unit_prices);
        for ($i = 0; $i < $itemNmber; $i++) {
            $currency = $item->currency;

            $quantity = $item->qtys[$i];
            $unitPrice = $item->unit_prices[$i];
            $vat_item = $item->vat_item[$i];
            $amount = ((float)$quantity * (float)$unitPrice) + (float)$vat_item;

            $totalAmountUSD = $this->getUSDAmount($amount, $currency);
            $totalAmountKHR = $this->getKHRAmount($amount, $currency);

            $unitPriceUSD = $this->getUSDAmount($unitPrice, $currency);
            $unitPriceKHR = $this->getKHRAmount($unitPrice, $currency);
            $vatUSD = $this->getUSDAmount($vat_item, $currency);
            $vatKHR = $this->getKHRAmount($vat_item, $currency);
            $payment_remaining = Budgetcode::where('budget_code', $item->budget_codes[$i])->first();

            AdvanceFormDetail::create([
                'req_recid'               => $this->req_recid,
                'procurment_body_id'      => $item->item_ids ? $item->item_ids[$i] : null,
                'invoice_number'          => $item->invoices[$i],
                'description'             => $item->descriptions[$i],
                'department_code'         => $item->department_codes[$i],
                'budget_code'             => $item->budget_codes[$i],
                'alternative_budget_code' => $item->alternative_budget_codes[$i],
                'unit'                    => $item->units[$i],
                'quantity'                => $item->qtys[$i],
                'exchange_rate_khr'       => $this->currentExchangeRate(),
                'unit_price_usd'          => $unitPriceUSD,
                'total_amount_usd'        => $totalAmountUSD,
                'unit_price_khr'          => $unitPriceKHR,
                'vat_item'          => $vatUSD,
                'vat_item_khr'          => $vatKHR,
                'total_amount_khr'        => $totalAmountKHR,
                'old_payment_remaining'     => $payment_remaining->payment_remaining,
                'within_budget'           => 'NOT YET CALCULATED',
            ]);
        }
        
    }

    public function blockProcurementBodyItem()
    {
        DB::transaction(function () {
            $advanceDetails = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
            foreach ($advanceDetails as $detial) {
                if ($detial->procurment_body_id) {
                    Procurementbody::where('id', $detial->procurment_body_id)->update([
                        'used_by_request'=>$this->req_recid,
                    ]);
                }
            }
        });
    }

    public function freeProcurmentBody()
    {
        DB::transaction(function () {
            $advanceDetails = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
            foreach ($advanceDetails as $detial) {
                if ($detial->procurment_body_id) {
                    Procurementbody::where('id', $detial->procurment_body_id)->update([
                        'used_by_request' => null,
                        'paid'  => 'N',
                    ]);
                }
            }
        });
    }

    public function updateTotalAmount()
    {
        $details = AdvanceFormDetail::select(DB::raw('sum(total_amount_usd) as totalUSD, sum(total_amount_khr) as totalKHR'))
                ->where('req_recid', $this->req_recid)
                ->groupBy('req_recid')
                ->get();
        if ($details[0]) {
            $detail = (object)$details[0];
            AdvanceForm::where('req_recid', $this->req_recid)->update([
                'total_amount_usd' => $detail->totalUSD,
                'total_amount_khr' => $detail->totalKHR,
            ]);
        }
    }

    public function updateFormDetailTotalaBudgetAmount($action)
    {
        /**find advance form detail */
        $details = AdvanceFormDetail::where('req_recid', $this->req_recid)->orderBy('id', 'asc')->get();

        /** find budgets */
        $bugetCodes = collect($details)->pluck('budget_code');
        $budgets = Budgetcode::whereIn('budget_code', $bugetCodes)->get();

        /** find alternative budgets */
        $atlBudgetCodes = collect($details)->pluck('alternative_budget_code');
        $alternativeBudgets = Budgetcode::whereIn('budget_code', $atlBudgetCodes)->get();

        /** process calculate and update advance detail */
        foreach ($details as $detail) {
            $budget = collect($budgets)->firstWhere('budget_code', $detail->budget_code);
            if ($budget) {
                $requestAmount     = $detail->total_amount_usd > 0 ? $detail->total_amount_usd : 0;
                $totalRemainAmount = $budget->payment_remaining > 0 ? $budget->payment_remaining : 0;
                
                /**when payment remain is buger or equal request amount so we no need to care about alternative budget code */
                if ($totalRemainAmount >= $requestAmount) {
                    $totalBudget     = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = (float)$totalBudget - (float)$totalRemainAmount;
                    
                    /**update detial */
                    AdvanceFormDetail::where('id', $detail->id)->update([
                        'total_budget_amount'             => $totalBudget,
                        'total_budget_amount_used'        => $requestAmount,
                        'total_budget_ytd_expense_amount' => $totalYTDExpense > 0 ? $totalYTDExpense : 0,
                        'within_budget'                   => BudgetEnum::WithinBudget()
                    ]);
                    if($action != ActionEnum::Save()){
                        //insert data into budget history
                        $budget_hsitory = new Budgethistory();
                        $budget_hsitory->req_recid = $this->req_recid;
                        $budget_hsitory->budget_code = $detail->budget_code;
                        $budget_hsitory->alternative_budget_code = $detail->alternative_budget_code;
                        $budget_hsitory->budget_amount_use = $requestAmount;
                        $budget_hsitory->alternative_amount_use = 0;
                        $budget_hsitory->save();
                    }
                    

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail, $totalRemainAmount, $requestAmount) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $totalRemain = (float)$totalRemainAmount - (float)$requestAmount;
                            $code['payment_remaining'] = $totalRemain > 0 ? $totalRemain : 0;
                        }
                        return $code;
                    });
                    continue;
                }

                /** check if user not provide alternative code while budget is smaller then request amount */
                if (
                    !$detail->alternative_budget_code
                    || $detail->alternative_budget_code == 'NA'
                    || $detail->alternative_budget_code == 'NO'
                    || $detail->alternative_budget_code == 'NO01'
                    || $detail->alternative_budget_code == 'N/A') {
                    $totalBudget     = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = (float)$totalBudget - (float)$totalRemainAmount;

                    /**update detial */
                    AdvanceFormDetail::where('id', $detail->id)->update([
                        'total_budget_amount'             => $totalBudget,
                        'total_budget_amount_used'        => $requestAmount,
                        'total_budget_ytd_expense_amount' => $totalYTDExpense > 0 ? $totalYTDExpense : 0,
                        'within_budget'                   => BudgetEnum::NotWithinBudget()
                    ]);

                    //insert data into budget history
                    if($action != ActionEnum::Save()){
                        $budget_hsitory = new Budgethistory();
                        $budget_hsitory->req_recid = $this->req_recid;
                        $budget_hsitory->budget_code = $detail->budget_code;
                        $budget_hsitory->alternative_budget_code = $detail->alternative_budget_code;
                        $budget_hsitory->budget_amount_use = $requestAmount;
                        $budget_hsitory->alternative_amount_use = 0;
                        $budget_hsitory->save();
                    }

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail, $totalRemainAmount, $requestAmount) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $total = (float)$totalRemainAmount - (float)$requestAmount;
                            $code['payment_remaining'] = $total > 0 ? $total : 0;
                        }
                        return $code;
                    });


                    continue;
                }


                /** incase paymentremain is smaller then request amount and user also select alternative code */
                $altBudget = collect($alternativeBudgets)->firstWhere('budget_code', $detail->alternative_budget_code);
                if ($altBudget) {
                    /**process budget code */
                    $totalBudgetUsed = $totalRemainAmount;
                    $totalBudget     = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = (float)$totalBudget - (float)$totalRemainAmount;

                    $field['total_budget_amount'] = $budget->total;
                    $field['total_budget_amount_used'] = $totalBudgetUsed;
                    $field['total_budget_ytd_expense_amount'] = $totalYTDExpense > 0 ? $totalYTDExpense : 0;

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $code['payment_remaining'] = 0;
                        }
                        return $code;
                    });

                    /** process alter native budget */
                    $totalAltRemainAmount = $altBudget->payment_remaining > 0 ? $altBudget->payment_remaining : 0;
                    $totalAltUsed         = (float)$requestAmount - (float)$totalBudgetUsed;
                    $totalAltBudget       = $altBudget->total > 0 ? $altBudget->total : 0;
                    $totalAltYTDExpense   = (float)$altBudget->total - (float)$totalAltRemainAmount;

                    $field['total_alt_budget_amount'] = $totalAltBudget;
                    $field['total_alt_budget_amount_used'] = $totalAltUsed > 0 ? $totalAltUsed : 0;
                    $field['total_alt_budget_ytd_expense_amount'] = $totalAltYTDExpense > 0 ? $totalAltYTDExpense : 0;

                    /** check is within budget */
                    $totalRemaining = (float)$totalRemainAmount + (float)$totalAltRemainAmount;
                    if ($requestAmount > $totalRemaining) {
                        $field['within_budget'] =  BudgetEnum::NotWithinBudget();
                    } else {
                        $field['within_budget'] =  BudgetEnum::WithinBudget();
                    }

                    /**update detial */
                    AdvanceFormDetail::where('id', $detail->id)->update($field);

                    //insert data into budget history
                    if($action != ActionEnum::Save()){
                        $budget_hsitory = new Budgethistory();
                        $budget_hsitory->req_recid = $this->req_recid;
                        $budget_hsitory->budget_code = $detail->budget_code;
                        $budget_hsitory->alternative_budget_code = $detail->alternative_budget_code;
                        $budget_hsitory->budget_amount_use = $totalBudgetUsed;
                        $budget_hsitory->alternative_amount_use = $totalAltUsed;
                        $budget_hsitory->save();
                    }
                    
                    /**update total remaining amount */
                    $alternativeBudgets = collect($alternativeBudgets)->map(function ($budget) use ($detail, $totalAltRemainAmount, $totalAltUsed) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $total = (float)$totalAltRemainAmount - (float)$totalAltUsed;
                            $code['payment_remaining'] = $total > 0 ? $total : 0;
                        }
                        return $code;
                    });

                    continue;
                }
            }
        }
    }

    public function previewBeforSubmit()
    {
        /**find advance form detail */
        $details = AdvanceFormDetail::where('req_recid', $this->req_recid)->orderBy('id', 'asc')->get();

        /** find budgets */
        $bugetCodes = collect($details)->pluck('budget_code');
        $budgets    = Budgetcode::whereIn('budget_code', $bugetCodes)->get();

        /** find alternative budgets */
        $atlBudgetCodes     = collect($details)->pluck('alternative_budget_code');
        $alternativeBudgets = Budgetcode::whereIn('budget_code', $atlBudgetCodes)->get();

        $paymentPreviews = [];

        /** process calculate and update advance detail */
        foreach ($details as $detail) {

            /**find budget code */
            $budget = collect($budgets)->firstWhere('budget_code', $detail->budget_code);
            if ($budget) {
                $requestAmount     = $detail->total_amount_usd > 0 ? $detail->total_amount_usd : 0;
                $totalRemainAmount = $budget->payment_remaining > 0 ? $budget->payment_remaining : 0 ;

                /**when payment remain is buger or equal request amount so we no need to care about alternative budget code */
                if ($totalRemainAmount >= $requestAmount) {
                    $totalBudget     = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = ((float)$totalBudget - (float)$totalRemainAmount)  + (float)$requestAmount;
                    $total_remaining_amount = (float)$totalRemainAmount - (float)$requestAmount;
                    /**update detial */
                    array_push(
                        $paymentPreviews,
                        [
                        'budget_code'             => $detail->budget_code,
                        'alternative_budget_code' => $detail->alternative_budget_code != 0 ? $detail->alternative_budget_code : 'N/A',
                        'total_request'           => $requestAmount,
                        'total_budget'            => $budget->total,
                        'ytd_expense'             => $totalYTDExpense > 0 ? $totalYTDExpense : 0,
                        'total_remaining_amount'  => $total_remaining_amount > 0 ? $total_remaining_amount : 0,
                        'status'                  => BudgetEnum::WithinBudgetDescription()
                    ]
                    );

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail, $totalRemainAmount, $requestAmount) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $totalRemaining = (float)$totalRemainAmount - (float)$requestAmount;
                            $code['payment_remaining'] = $totalRemaining > 0 ? $totalRemaining : 0;
                        }
                        return $code;
                    });
                    continue;
                }

                /** check if user not provide alternative code while budget is smaller then request amount */
                if (
                    !$detail->alternative_budget_code
                    || $detail->alternative_budget_code == 'NA'
                    || $detail->alternative_budget_code == 'NO'
                    || $detail->alternative_budget_code == 'NO01'
                    || $detail->alternative_budget_code == 'N/A') {
                    $totalBudget = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = ((float)$totalBudget - (float)$totalRemainAmount) + (float)$requestAmount;
                    $total_remaining_amount = (float)$totalRemainAmount - (float)$requestAmount;
                    /**update detial */
                    array_push(
                        $paymentPreviews,
                        [
                        'budget_code'             => $detail->budget_code,
                        'alternative_budget_code' => $detail->alternative_budget_code != 0 ? $detail->alternative_budget_code : 'N/A',
                        'total_request'           => $requestAmount,
                        'total_budget'            => $totalBudget,
                        'ytd_expense'             => $totalYTDExpense > 0 ? $totalYTDExpense : 0,
                        'total_remaining_amount'  => $total_remaining_amount > 0 ? $total_remaining_amount : 0,
                        'status'                  => BudgetEnum::NotWithinBudgetDescription()
                    ]
                    );

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail, $totalRemainAmount, $requestAmount) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $total =(float)$totalRemainAmount - (float)$requestAmount;
                            $code['payment_remaining'] = $total > 0 ? $total : 0;
                        }
                        return $code;
                    });
                    continue;
                }


                /** incase paymentremain is smaller then request amount and user also select alternative code */
                $altBudget = collect($alternativeBudgets)->firstWhere('budget_code', $detail->alternative_budget_code);
                if ($altBudget) {
                    /**process budget code */
                    $totalBudgetUsed = $totalRemainAmount;
                    $totalBudget     = $budget->total > 0 ? $budget->total : 0;
                    $totalYTDExpense = ((float)$totalBudget - (float)$totalRemainAmount) + (float)$totalBudgetUsed;

                    $field['total_budget_amount'] = $totalRemainAmount;
                    $field['total_budget_amount_used'] = $totalBudgetUsed ;
                    $field['total_budget_ytd_expense_amount'] = $totalYTDExpense > 0 ? $totalYTDExpense : 0;

                    /**update total remaining amount */
                    $budgets = collect($budgets)->map(function ($budget) use ($detail) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $code['payment_remaining'] = 0;
                        }
                        return $code;
                    });

                    /** process alter native budget */
                    $totalAltRemainAmount = $altBudget->payment_remaining > 0 ? $altBudget->payment_remaining : 0;
                    $totalAltUsed         = (float)$requestAmount - (float)$totalBudgetUsed;
                    $totalAlBudget        = $altBudget->total > 0 ? $altBudget->total : 0;
                    $totalAltYTDExpense   = ((float)$totalAlBudget - (float)$totalAltRemainAmount) + (float)$totalAltUsed;

                    $field['total_alt_budget_amount'] = $totalAltRemainAmount > 0 ? $totalAltRemainAmount : 0;
                    $field['total_alt_budget_amount_used'] = $totalAltUsed > 0 ? $totalAltUsed : 0;
                    $field['total_alt_budget_ytd_expense_amount'] = $totalAltYTDExpense > 0 ? $totalAltYTDExpense : 0;

                    /** check is within budget */
                    $total_remaining_amount = (float)$totalRemainAmount - (float)$totalBudgetUsed;
                    $total_alt_remaining_amount = (float)$totalAltRemainAmount - (float)$totalAltUsed;
                    $totalRemaining = $total_remaining_amount + $total_alt_remaining_amount;

                    if ($requestAmount > $totalRemaining) {
                        $totalBudget = $budget->total + $altBudget->total;
                        $totalYtd = $totalYTDExpense + $totalAltYTDExpense;
                        array_push(
                            $paymentPreviews,
                            [
                            'budget_code'             => $detail->budget_code,
                            'alternative_budget_code' => $detail->alternative_budget_code != 0 ? $detail->alternative_budget_code : 'N/A',
                            'total_request'           => $requestAmount > 0 ? $requestAmount : 0,
                            'total_budget'            => $totalBudget > 0 ? $totalBudget : 0 ,
                            'ytd_expense'             => $totalYtd > 0 ? $totalYtd : 0 ,
                            'total_remaining_amount'  => $totalRemaining ,
                            'status'                  => BudgetEnum::NotWithinBudgetDescription()
                        ]
                        );
                    } else {
                        array_push(
                            $paymentPreviews,
                            [
                            'budget_code'             => $detail->budget_code,
                            'alternative_budget_code' => $detail->alternative_budget_code != 0 ? $detail->alternative_budget_code : 'N/A',
                            'total_request'           => $requestAmount > 0 ? $requestAmount : 0,
                            'total_budget'            => $budget->total + $altBudget->total ,
                            'ytd_expense'             => $totalYTDExpense + $totalAltYTDExpense ,
                            'total_remaining_amount'  => $totalRemaining,
                            'status'                  => BudgetEnum::WithinBudgetDescription()
                        ]
                        );
                    }
                    /**update total remaining amount */
                    $alternativeBudgets = collect($alternativeBudgets)->map(function ($budget) use ($detail, $totalAltRemainAmount, $totalAltUsed) {
                        $code = $budget;
                        if ($budget->budget_code == $detail->budget_code) {
                            $total = (float)$totalAltRemainAmount - (float)$totalAltUsed;
                            $code['payment_remaining'] = $total > 0 ? $total : 0;
                        }
                        return $code;
                    });

                    continue;
                }
            }
        }

        return $paymentPreviews;
    }

    public function getAllUserLevelForApproval()
    {
        /** find is that request is within budget */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return null;
        }

        $withinBudget = $tasklist->within_budget;
        $totalAmountRequest = $this->total_amount_usd;

        $flowConfigs = $this->getCurrentFlowConfig();

        $approverLevels =[];
        $currentUser = Auth::user();
        foreach ($flowConfigs as $flow) {

            /** if first reviewer */
            if ($flow->checker == 'first_reviewer') {
                $users = $this->findFirstReviewer($tasklist, $currentUser);
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'First Reviewer',
                    'users'        => $users,
                    'allow_select' => true,
                    'form_control' => 'dropdown',
                    'form_controle_readonly'=> false
                ]);
            }
            
            /** accounting reviewer */
            if ($flow->checker == 'accounting') {
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'Accounting Review',
                    'users'        => 'accounting',
                    'allow_select' => false,
                    'form_control' => 'input',
                    'form_controle_readonly'=> true
                ]);
            }

            /** approvers */
            if ($flow->checker == 'approver') {
                $users = $this->findApprover($totalAmountRequest, $withinBudget);
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'Approver',
                    'users'        => $users,
                    'allow_select' => true,
                    'form_control' => 'dropdown',
                    'form_controle_readonly'=> false,
                ]);
            }

            /** approver as cfo */
            if ($flow->checker == 'approver_cfo') {
                $users = $this->findApproverCFO();
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'CFO',
                    'users'        => $users,
                    'allow_select' => false,
                    'form_control' => 'input',
                    'form_controle_readonly'=> true
                ]);
            }

            /** approver as md office */
            if ($flow->checker == 'md_office') {
                $users = $this->findApproverMdOffice();
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'MD OFFICE',
                    'users'        => $users,
                    'allow_select' => false,
                    'form_control' => 'input',
                    'form_controle_readonly'=> true
                ]);
            }

            /** approver as md office */
            if ($flow->checker == 'approver_ceo') {
                $users = $this->findApproverCEO();
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'CEO',
                    'users'        => $users,
                    'allow_select' => false,
                    'form_control' => 'input',
                    'form_controle_readonly'=> true
                ]);
            }

            /** approver as md office */
            if ($flow->checker == 'accounting_finance') {
                $users = $this->findApproverCEO();
                array_push($approverLevels, [
                    'checker'      =>$flow->checker,
                    'step_number'  => $flow->step_number,
                    'label'        => 'Payment Process',
                    'users'        => 'accounting',
                    'allow_select' => false,
                    'form_control' => 'input',
                    'form_controle_readonly'=> true
                ]);
            }
        }

        $approverLevels = collect($approverLevels)->transform(function ($approver) {
            return (object)$approver;
        });
        return $approverLevels;
    }

    public function saveApprovalLevel($request, $req_recid, $first_reviewer, $approver)
    {
        $review_approve = Reviewapprove::firstOrNew(['req_recid' => $req_recid]);
        $review_approve->req_recid = $req_recid;
        $review_approve->approve = 'NO APPROVER';
        $flows = $this->getCurrentFlowConfig();
        foreach ($flows as $flow) {
            if ($flow->checker == 'first_reviewer') {
                /** when user also choose first reviewer */
                if ($first_reviewer) {
                    $approve = explode('/', $first_reviewer);
                    $approve_email = $approve[0];
                    $review_approve->review = $approve_email;
                }
            }
            if ($flow->checker == 'second_reviewer') {
                /** when user also choose second reviewer */
                if ($request->second_reviewer) {
                    $approve = explode('/', $request->second_reviewer);
                    $approve_email = $approve[0];
                    $review_approve->second_review = $approve_email;
                }
            }
            if ($flow->checker == 'third_reviewer') {
                /** when user also choose Third reviewer */
                if ($request->third_reviewer) {
                    $approve = explode('/', $request->third_reviewer);
                    $approve_email = $approve[0];
                    $review_approve->third_review = $approve_email;
                }
            }
            if ($flow->checker == 'fourth_reviewer') {
                /** when user also choose fourth reviewer */
                if ($request->fourth_reviewer) {
                    $approve = explode('/', $request->fourth_reviewer);
                    $approve_email = $approve[0];
                    $review_approve->fourth_reviewer = $approve_email;
                }
            }

            /** for accounting level */
            if ($flow->checker == 'accounting') {
                $review_approve->budget_owner = 'accounting';
            }

            /** for approver level */
            if ($flow->checker == 'approver') {
                $approve = explode('/', $approver);
                $approve_email = $approve[0];
                $review_approve->approve = $approve_email;
            }
        }
        $review_approve->save();
        $review_approve->refresh();

        /** alert to pending user */
        $tasklist = Tasklist::firstWhere('req_recid', $req_recid);
        if ($first_reviewer) {
            $approve = explode('/', $first_reviewer);
            $checker_email = $approve[0];
            $checker_role  = $approve[1];
            $tasklist->update([
                'next_checker_group' => $checker_email,
                'next_checker_role'  => $checker_role,
                'step_number'        => 1,
                'req_status'         => '002'
            ]);
        }
        /** if user skip first reviewer, then based on flow it always alert to step 2 team */
        else {
            $nextChecker = collect($flows)->firstWhere('step_number', 5);
            if ($nextChecker) {
                $checker = (object)$nextChecker;

                if ($checker->checker == 'accounting') {
                    $tasklist->update([
                        'next_checker_group' => 'accounting',
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }

                if ($checker->checker == 'approver') {
                    $tasklist->update([
                        'next_checker_group' => $review_approve->approve,
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }

                if ($checker->checker == 'md_office') {
                    $groupId = Groupid::where('groupid.group_id', 'GROUP_MDOFFICE')->first();
                    $tasklist->update([
                        'next_checker_group' => $groupId->email,
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }

                if ($checker->checker == 'approver_cfo') {
                    $groupId = Groupid::where('groupid.group_id', 'GROUP_CFO')->where('groupid.is_cfo','1')->first();
                    $tasklist->update([
                        'next_checker_group' => $groupId->email,
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }

                if ($checker->checker == 'approver_ceo') {
                    $groupId = Groupid::where('groupid.group_id', 'GROUP_CEO')->first();
                    $tasklist->update([
                        'next_checker_group' => $groupId->email,
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }

                if ($checker->checker == 'accounting_finance') {
                    $tasklist->update([
                        'next_checker_group' => 'accounting',
                        'next_checker_role'  => 2,
                        'step_number'        => 5,
                        'req_status'         => '002'
                    ]);
                }
            }
        }
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

    public function updateTotalBudget()
    {
        $advanceDetails = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
        foreach ($advanceDetails as $detail) {
            $totalYTDExpense = 0;
            $altTotalYTDExpense = 0;

            if ($detail->budget_code) {
                $budgetCode = Budgetcode::firstWhere('budget_code', $detail->budget_code);
                if ($budgetCode) {
                    $totalRemainAmount = (float)$budgetCode->payment_remaining - (float)$detail->total_budget_amount_used;
                    $total =  $totalRemainAmount >= 0 ? $totalRemainAmount : 0;
                    $budgetCode->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
                    $budgetCode->refresh();

                    $totalYTDExpense = (float)$budgetCode->total - (float)$budgetCode->payment_remaining;
                }
            }

            if ($detail->alternative_budget_code) {
                $altBudgetCode = Budgetcode::firstWhere('budget_code', $detail->alternative_budget_code);
                if ($altBudgetCode) {
                    $totalRemainAmount = (float)$altBudgetCode->payment_remaining - (float)$detail->total_alt_budget_amount_used;
                    $total =  $totalRemainAmount >= 0 ? $totalRemainAmount : 0;
                    $altBudgetCode->update([
                        'payment'           => $total,
                        'temp_payment'      => $total,
                        'payment_remaining' => $total,
                    ]);
                    $altBudgetCode->refresh();
                    $altTotalYTDExpense = (float)$altBudgetCode->total - (float)$altBudgetCode->payment_remaining;
                }
            }

            AdvanceFormDetail::where('id', $detail->id)->update([
                'total_budget_ytd_expense_amount'     => $totalYTDExpense >= 0 ? $totalYTDExpense : 0,
                'total_alt_budget_ytd_expense_amount' => $altTotalYTDExpense >= 0 ? $altTotalYTDExpense : 0,
            ]);
        }
    }

    private function findApproverCEO()
    {
        $approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->where('groupid.group_id', 'GROUP_CEO')
                        ->first();
        if ($approver) {
            return "{$approver->firstname} {$approver->lastname}";
        }
    }
    private function findApproverMdOffice()
    {
        $approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->where('groupid.group_id', 'GROUP_MDOFFICE')
                        ->first();
        if ($approver) {
            return "{$approver->firstname} {$approver->lastname}";
        }
    }

    private function findApproverCFO()
    {
        $approver = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->where('groupid.group_id', 'GROUP_CFO')
                        ->where('groupid.is_cfo','1')
                        ->first();
        if ($approver) {
            return "{$approver->firstname} {$approver->lastname}";
        }
    }

    private function findApprover($totalRequestAmount, $withinBudget)
    {
        if ($totalRequestAmount <= 10000 && $withinBudget == BudgetEnum::WithinBudget()) {
            $approvers = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                        ->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')
                        ->get();
            return $approvers;
        }

        if ($totalRequestAmount <= 10000 && $withinBudget == BudgetEnum::NotWithinBudget()) {
            $approvers = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                        ->where('groupid.group_id', 'GROUP_SECONDLINE_EXCO')
                        ->get();
            return $approvers;
        }

        if ($totalRequestAmount > 10000 && $totalRequestAmount <= 50000 && $withinBudget == BudgetEnum::WithinBudget()) {
            $approvers = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                        ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                        ->where('groupid.group_id', 'GROUP_MEMBER_EXCO')
                        ->get();
            return $approvers;
        }

        $approvers = Groupid::join('usermgt', 'usermgt.email', 'groupid.email')
                    ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                    ->where('groupid.group_id', 'GROUP_MEMBER_EXCO')
                    ->get();
        return $approvers;
    }

    private function findFirstReviewer($tasklist, $currentUser)
    {
        $checker = Groupid::where('email', $tasklist->req_email)->where('role_id', '!=', 4)->first();
        if (!$checker) {
            return [];
        }

        $users = Groupid::join('usermgt', 'groupid.email', '=', 'usermgt.email')
                ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                ->where('groupid.group_id', $checker->group_id)
                ->where('groupid.email', '!=', $currentUser->email)
                ->where('groupid.role_id', '!=', '1')
                ->get();
        return $users;
    }
    public function getAllUserLevelReviewer(){
        $users = Groupid::join('usermgt', 'groupid.email', '=', 'usermgt.email')
                ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                ->where('groupid.role_id', '!=', '1')
                ->groupBy('groupid.email')
                ->get();
        return $users;
    }
    private function findReviewer($tasklist, $currentUser){
        $checker = Groupid::where('role_id', '!=', 1)->first();
        if (!$checker) {
            return [];
        }

        $users = Groupid::join('usermgt', 'groupid.email', '=', 'usermgt.email')
                ->select('groupid.email', 'groupid.role_id', 'usermgt.firstname', 'usermgt.lastname')
                ->where('groupid.group_id', $checker->group_id)
                ->where('groupid.email', '!=', $currentUser->email)
                ->where('groupid.role_id', '!=', '1')
                ->get();
        return $users;
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

    public function isAlreadySubmitted()
    {
        /**find task list */
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if ($tasklist->req_status == RequestStatusEnum::Save()) {
            return false;
        }

        return true;
    }

    public function saveLog($comment, $doer_role, $doer_action)
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
            'activity_form'        => FormTypeEnum::AdvanceFormRequest(),
            'activity_datetime'    => Carbon::now()->toDayDateTimeString(),
            'doer_role'            => $doer_role,
            'doer_action'          => $doer_action
        ]);
    }

    public function getUserApprovalLevel()
    {
        $flows = $this->getCurrentFlowConfig();
        $levelApprovers = [];

        $reviewApprover = Reviewapprove::firstWhere('req_recid', $this->req_recid);
        if (!$reviewApprover) {
            return [];
        }

        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return [];
        }

        foreach ($flows as $flow) {
            $isPending = false;
            if ($tasklist->step_number == $flow->step_number) {
                $isPending = true;
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

    public function sendEmailToPendingUser($comment)
    {
        /**@var Tasklist $tasklist */
        $requester = Requester::firstWhere('req_recid', $this->req_recid);
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        $groupId = $tasklist->next_checker_group;
        if($groupId === 'accounting'){
            $groupId = 'GROUP_ACCOUNTING';
        }
        $find_mail_group = Emailgroup::where('group_id', $groupId)->first();
        if (!empty($find_mail_group)) {
            $checker_email = $find_mail_group->group_email;
        }else{
            $checker_email = $tasklist->next_checker_group;
        }
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmailProcurementRequest(
            $content         = 'You have one request pending on your approval, Please check.',
            $rec_id          = $this->req_recid,
            $req_name        = $tasklist->req_name,
            $req_branch      = $tasklist->req_branch,
            $req_position    = $tasklist->req_position,
            $subject         = 'Advance form request',
            $checker         = $checker_email,
            $cc              = $tasklist->req_email,
            $comment         = $comment,
            $request_subject =  $requester->subject
        );
        return $result;
    }

    public function sendEmailFormHasBeenQueriedBack($comment)
    {
        /**@var Tasklist $tasklist */
        $requester = Requester::firstWhere('req_recid', $this->req_recid);
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmailProcurementRequest(
            $content      = 'Your request has been queried back, Please check.',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Advance form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment,
            $request_subject =  $requester->subject
        );
        return $result;
    }

    public function sendEmailFormHasBeenAssignedBack($comment)
    {
        /**@var Tasklist $tasklist */
        $requester = Requester::firstWhere('req_recid', $this->req_recid);
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmailProcurementRequest(
            $content      = 'Your request has been assigned back, Please check.',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Advance form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment,
            $request_subject =  $requester->subject
        );
        return $result;
    }
    public function sendEmailFormHasBeenRejected($comment)
    {
        /**@var Tasklist $tasklist */
        $requester = Requester::firstWhere('req_recid', $this->req_recid);
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmailProcurementRequest(
            $content      = 'Your request has been rejected',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Advance form request',
            $checker      = $tasklist->req_email,
            $cc           = $tasklist->next_checker_group,
            $comment      = $comment,
            $request_subject =  $requester->subject
        );
        return $result;
    }

    public function sendEmailFormHasBeenApproved($comment)
    {
        /**@var Tasklist $tasklist */
        $requester = Requester::firstWhere('req_recid', $this->req_recid);
        $tasklist = Tasklist::firstWhere('req_recid', $this->req_recid);
        $groupId = $tasklist->next_checker_group;
        if($groupId === 'accounting'){
            $groupId = 'GROUP_ACCOUNTING';
        }
        $find_mail_group = Emailgroup::where('group_id', $groupId)->first();
        if (!empty($find_mail_group)) {
            $checker_email = $find_mail_group->group_email;
        }else{
            $checker_email = $tasklist->next_checker_group;
        }
        if (!$tasklist) {
            return 'fail';
        }

        /** send email to requester when form has been approved completed */
        if ($tasklist->isApprovedCompleted()) {
            $emailService = new Sendemail();
            $result = $emailService->sendEmailProcurementRequest(
                $content      = 'Your request has been approved completed',
                $rec_id       = $this->req_recid,
                $req_name     = $tasklist->req_name,
                $req_branch   = $tasklist->req_branch,
                $req_position = $tasklist->req_position,
                $subject      = 'Advance form request',
                $checker      = $tasklist->req_email,
                $cc           = '',
                $comment      = $comment,
                $request_subject =  $requester->subject
            );
            return $result;
        };

        /** send email to next checker for approval */
        $emailService = new Sendemail();
        $result = $emailService->sendEmailProcurementRequest(
            $content      = 'Your request has been approved completed',
            $rec_id       = $this->req_recid,
            $req_name     = $tasklist->req_name,
            $req_branch   = $tasklist->req_branch,
            $req_position = $tasklist->req_position,
            $subject      = 'Advance form request',
            $checker      = $checker_email,
            $cc           = $tasklist->req_email,
            $comment      = $comment,
            $request_subject =  $requester->subject
        );
        return $result;
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
    function updateBudgetItem($old_amount,$new_amount,$budget_code){

        $budgetCode = Budgetcode::firstWhere('budget_code', $budget_code);
            if ($budgetCode) {
                $totalRemainAmount = (float)$budgetCode->payment_remaining + (float)$old_amount - (float)$new_amount;
                $totalAmountUse = (float)$this->total_amount_usd - (float)$old_amount + (float)$new_amount;
                
                $total =  $totalRemainAmount >= 0 ? $totalRemainAmount : 0;
                $budgetCode->update([
                    'payment'           => $total,
                    'temp_payment'      => $total,
                    'payment_remaining' => $total,
                ]);
                /**calculate budget_amount_use on budget history */
                $budgetHistory = Budgethistory::Where(['req_recid'=>$this->req_recid])->delete();
                $advanceDetail = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
                foreach ($advanceDetail as $key => $detail) {
                    $budgetHistoryNew = new Budgethistory();
                    $budgetHistoryNew->req_recid = $this->req_recid;
                    $budgetHistoryNew->budget_code = $detail->budget_code;
                    $budgetHistoryNew->alternative_budget_code = 0;
                    $budgetHistoryNew->budget_amount_use = $detail->total_amount_usd;
                    $budgetHistoryNew->alternative_amount_use = 0;
                    $budgetHistoryNew->save();
                }
            }
    }
    function updateDifferentBudgetItem($oldBudget,$newAmount,$oldAmount,$newBudget){
        /**deposit amount to old budget */
        $budgetCode = Budgetcode::firstWhere('budget_code', $oldBudget);
        if ($budgetCode) {
            $budgetCode->update([
                'payment'           => (float)$budgetCode->payment + (float)$oldAmount,
                'temp_payment'      => (float)$budgetCode->temp_payment + (float)$oldAmount,
                'payment_remaining' => (float)$budgetCode->payment_remaining + (float)$oldAmount,
            ]);
        }
        /**cut amount from new budget */
        $newBudgetCode = Budgetcode::firstWhere('budget_code', $newBudget);
        if ($newBudgetCode) {
            $totalRemainAmount = (float)$newBudgetCode->payment_remaining - (float)$newAmount;
            $total =  $totalRemainAmount >= 0 ? $totalRemainAmount : 0;
            $newBudgetCode->update([
                'payment'           => $total,
                'temp_payment'      => $total,
                'payment_remaining' => $total,
            ]);
        }
        /**calculate old budget_amount_use on budget history */
        $budgetHistory = Budgethistory::Where(['req_recid'=>$this->req_recid])->delete();
        $advanceDetail = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
        foreach ($advanceDetail as $key => $detail) {
            $budgetHistoryNew = new Budgethistory();
            $budgetHistoryNew->req_recid = $this->req_recid;
            $budgetHistoryNew->budget_code = $detail->budget_code;
            $budgetHistoryNew->alternative_budget_code = 0;
            $budgetHistoryNew->budget_amount_use = $detail->total_amount_usd;
            $budgetHistoryNew->alternative_amount_use = 0;
            $budgetHistoryNew->save();
        }
    }
    public function addAmountToBudget(){
        $advanceDetails = AdvanceFormDetail::where('req_recid',$this->req_recid)->get();
        foreach($advanceDetails as $detail){
            $advancedBudgetCode = $detail->budget_code;
            $advanceAmount = $detail->total_amount_usd;
            $budgetCode = Budgetcode::firstWhere('budget_code', $advancedBudgetCode);
            $budgetAmountRemaining = $budgetCode->payment_remaining;
            $amountAfterCal = (float)$budgetAmountRemaining + (float)$advanceAmount;
            /**update amount to budget after rejected */
            $budgetCode->update([
                'payment'           => $amountAfterCal,
                'temp_payment'      => $amountAfterCal,
                'payment_remaining' => $amountAfterCal,
            ]);
        }
        /** delete record in budget history after rejected request */
        $budgetHistory = Budgethistory::where('req_recid',$this->req_recid)->delete();
    }
    public function updateTotalBudgetItem($budgetCode,$altBudgetCode,$amount){
        $budgetCodeDetail = Budgetcode::firstWhere('budget_code', $budgetCode);
        $totalAmountBudgetCode = (float)$budgetCodeDetail->payment_remaining - (float)$amount;
        /**if user use Alternative Budget Code */
        if($altBudgetCode){
            $altBudgetCodeDetail = Budgetcode::firstWhere('budget_code', $altBudgetCode);
            $totalAmount = (float)$altBudgetCodeDetail->payment_remaining - (float)$amount;
            $altBudgetCodeDetail->update([
                'payment'           => $totalAmount,
                'temp_payment'      => $totalAmount,
                'payment_remaining' => $totalAmount,
            ]);
        }
        $budgetCodeDetail->update([
            'payment'           => $totalAmountBudgetCode,
            'temp_payment'      => $totalAmountBudgetCode,
            'payment_remaining' => $totalAmountBudgetCode,
        ]);
        /**Add to budget history */
        $budgetHistoryNew = new Budgethistory();
        $budgetHistoryNew->req_recid = $this->req_recid;
        $budgetHistoryNew->budget_code = $budgetCode;
        $budgetHistoryNew->alternative_budget_code = 0;
        $budgetHistoryNew->budget_amount_use = $amount;
        $budgetHistoryNew->alternative_amount_use = 0;
        $budgetHistoryNew->save();
    }
    public function updateBudgetAfterDelete($amount, $budget_code){
        $budgetCodeDetail = Budgetcode::firstWhere('budget_code', $budget_code);
        if($budgetCodeDetail){
            $totalAmountBudgetCode = (float)$budgetCodeDetail->payment_remaining + (float)$amount;
            $budgetCodeDetail->update([
                'payment'           => $totalAmountBudgetCode,
                'temp_payment'      => $totalAmountBudgetCode,
                'payment_remaining' => $totalAmountBudgetCode,
            ]);
        }
        /** delete record in budget history */
        $budgetHistory = Budgethistory::where('req_recid',$this->req_recid)->delete();
        $advanceDetail = AdvanceFormDetail::where('req_recid', $this->req_recid)->get();
        foreach ($advanceDetail as $key => $detail) {
            $budgetHistoryNew = new Budgethistory();
            $budgetHistoryNew->req_recid = $this->req_recid;
            $budgetHistoryNew->budget_code = $detail->budget_code;
            $budgetHistoryNew->alternative_budget_code = 0;
            $budgetHistoryNew->budget_amount_use = $detail->total_amount_usd;
            $budgetHistoryNew->alternative_amount_use = 0;
            $budgetHistoryNew->save();
        }
    } 
    public function checkroleForAdvamce($advanceForm){
        $taskList = Tasklist::firstWhere('req_recid', $advanceForm->req_recid);
        $withinBudget = $taskList->within_budget;
        $totalAmountRequest = $advanceForm->total_amount_usd;
        $flowAmountRequest = $advanceForm->getRequestAmountForFlowConfig($totalAmountRequest);
        $nextStep = $taskList->step_number;

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
        if($flowConfig->step_number <= 4){
            $doerRole = 'Reviewer';
        }else{
            $doerRole = $flowConfig->step_description;
        }
        $doerAction = $taskList->checkActionPayment($doerRole);
        
        return [$doerRole,$doerAction];
    }
}
