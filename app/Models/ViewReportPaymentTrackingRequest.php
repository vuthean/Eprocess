<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewReportPaymentTrackingRequest extends Model
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
        'created_at',
    ];
    protected $casts = [
        'created_at'     => 'date',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
