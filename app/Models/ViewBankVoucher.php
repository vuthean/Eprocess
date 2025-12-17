<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewBankVoucher extends Model
{
    use HasFactory;
    protected $table    = 'view_bank_voucher_tracking';
    protected $fillable = [
        'req_recid',
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
    ];
}
