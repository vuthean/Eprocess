<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ViewCashPaymentVoucher extends Model
{
    use HasFactory;
    protected $table    = 'view_cash_payment_voucher_tracking';
    protected $fillable = [
        'req_recid',
        'ref_no',
        'ref_type',
        'payment_method_code',
        'req_date',
        'requested_date',
        'reviewed_date',
        'approved_date',
        'requester',
        'reviewer',
        'approver',
        'paid_by',
        'paid_date',
        'record_status_description',
        'ccy',
        'total_amount',
        'account_name',
        'account_number',
        'exported_at',
        'due_date'
    ];
}
