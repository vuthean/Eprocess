<?php

namespace App\Models;

use App\Enums\FormTypeEnum;
use App\Myclass\Approval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Procurement extends Model
{
    use HasFactory;
    protected $table = 'procurement';
    protected $fillable = [
        'req_recid',
        'req_email',
        'req_date',
        'purpose',
        'bid',
        'justification',
        'comment_by_pr',
        'vat',  
        'grand_total'
    ];

    public function getTotalRequestAmount()
    {
        $procurementBody = Procurementbody::where('req_recid', $this->req_recid)
            ->select(DB::raw('SUM(total) AS total '))
            ->groupBy('req_recid')
            ->get();
        return $procurementBody[0]['total'];
    }

    public function findApproverForStep($stepNumber, $withinBudget)
    {
        /** find reviewer approver */
        $reviewer = Reviewapprove::firstWhere('req_recid', $this->req_recid);

        /**check if approver is ceo */
        $isCEO = 0;
        if ($reviewer->hasApproverAsCEO()) {
            $isCEO = 1;
        }
        /** find flow configure */
        $total = $this->getTotalRequestAmount();
        $amountRequest = '';
        if ($total <= 3000) {
            $amountRequest = '<=3000';
        } elseif ($total > 3000 and $total < 30000) {
            $amountRequest = '<=5000';
        } else {
            $amountRequest = '>5000';
        }

        if($withinBudget == 'N'){
            $flowConfigure = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
            ->where('within_budget', 'N')
            ->where('step_number', $stepNumber)
            ->where('version', 2)
            ->first();
        }else{
            $flowConfigure = Flowconfig::where('req_name', FormTypeEnum::ProcurementRequest())
            ->where('within_budget', 'Y')
            ->where('amount_request', $amountRequest)
            ->where('step_number', $stepNumber)
            ->where('approver_is_ceo', $isCEO)
            ->where('version', 2)
            ->first();

        }
        if ($flowConfigure->checker == 'first_reviewer') {
            return [
                    'next_checker_group' => $reviewer->review,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'second_reviewer') {
            return [
                    'next_checker_group' => $reviewer->second_review,
                    'step_number'        => $stepNumber
                ];
        }
        if ($flowConfigure->checker == 'third_reviewer') {
            return [
                    'next_checker_group' => $reviewer->third_review,
                    'step_number'        => $stepNumber
                ];
        }
        if ($flowConfigure->checker == 'forth_reviewer') {
            return [
                    'next_checker_group' => $reviewer->fourth_reviewer,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'budget_owner') {
            return [
                    'next_checker_group' => $reviewer->budget_owner,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'md_office') {
            $groupId = Groupid::firstWhere('group_id', 'GROUP_MDOFFICE');
            return [
                    'next_checker_group' => $groupId->email,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'approver_cfo') {
            $groupId = Groupid::firstWhere([['group_id', 'GROUP_CFO'],['is_cfo', '1']]);
            return [
                    'next_checker_group' => $groupId->email,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'approver') {
            return [
                    'next_checker_group' => $reviewer->approve,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'approver_ceo') {
            $groupId = Groupid::firstWhere('group_id', 'GROUP_CEO');
            return [
                    'next_checker_group' => $groupId->email,
                    'step_number'        => $stepNumber
                ];
        }

        if ($flowConfigure->checker == 'receiver') {
            return [
                    'next_checker_group' => $reviewer->final,
                    'step_number'        => $stepNumber
                ];
        }
    }
}
