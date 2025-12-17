<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewProcurementReportTracking extends Model
{
    use HasFactory;
    protected $table    = 'view_procurement_report_trackings';
    protected $fillable = [
        'req_recid',
        'req_date',
        'requested_date',
        'approved_date',
        'requester',
        'reviewer',
        'approver',
        'subject',
        'budget_code',
        'br_dep_code',
        'alternativebudget_code',
        'description',
        'currency',
        'quantity',
        'unit',
        'unit_price',
        'total_usd',
        'total_khr',
        'vat',
        'paid',
        'procured_by',
        'payment_date',
        'payment_ref_no',
        'bid',
        'advance_request',
        'date_of_adv',
        'clear_request',
        'date_of_adc'
    ];
}

