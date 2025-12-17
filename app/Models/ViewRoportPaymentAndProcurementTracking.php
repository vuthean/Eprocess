<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ViewRoportPaymentAndProcurementTracking extends Model
{
    use HasFactory;
    protected $table = 'view_report_payment_procurement_tracking_request';
    protected $fillable = [
        'rp_ref_no',
        'req_date',
        'approve_date',
        'requester',
        'approvers',
        'req_department',
        'subject',
        'ccy',
        'amount',
        'supplier_name',
        'payment_method',
        'budget_code',
        'alt_code',
        'paid_date',
        'paid_by',
        'status',
        'created_at',
        'department_code',
        'description',
        'qty',
        'unit_price',
        'ref',
        'pro_request_date',
        'received_date',
        'pro_requester',
        'procure_by',
        'req_pr_branch',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
