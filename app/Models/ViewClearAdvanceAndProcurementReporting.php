<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewClearAdvanceAndProcurementReporting extends Model
{
    use HasFactory;
    protected $table    = 'view_adc_adv_and_procure_tracking';
    protected $fillable = [
        'adc_ref',
        'subject',
        'req_date',
        'approved_date',
        'requester',
        'approver',
        'req_branch',
        'department_code',
        'budget_code',
        'alternative_budget_code',
        'description',
        'quantity',
        'unit',
        'total_amount_usd',
        'paid_date',
        'paid_by',
        'record_status_description',
        'ccy',
        'advance_ref_no',
        'adv_req_date',
        'adv_requester',
        'adv_paid_date',
        'adv_paid_by',
        'procurement_req',
        'procurement_req_date',
        'procurement_requester',
        'procurement_paid_date',
        'procurement_paid_by',
        'req_pr_branch'
    ];
}
