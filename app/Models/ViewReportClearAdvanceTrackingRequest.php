<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ViewReportClearAdvanceTrackingRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'rp_ref_no',
        'req_date',
        'approve_date',
        'line_review_date',
        'accounting_review_date',
        'requester',
        'reviewers',
        'approvers',
        'req_department',
        'subject',
        'ccy',
        'amount',
        'supplier_name',
        'payment_method',
        'budget_code',
        'alt_code',
        'budget_items',
        'total_budget',
        'ytd_expense',
        'total_budget_remaining',
        'paid_date',
        'paid_by',
        'status',
        'adc_ref_no',
        'clear_date',
        'created_at',
        'due_date'
    ];
    
}
