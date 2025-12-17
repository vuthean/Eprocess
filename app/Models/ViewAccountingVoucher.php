<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewAccountingVoucher extends Model
{
    use HasFactory;
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
    ];

    public function getPaidDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }

    public function getReqDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }
    public function getReviewedDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }
    public function getApprovedDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }
    public function getExportedAtAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }
}
