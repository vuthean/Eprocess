<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearAdvanceFormDetail extends Model
{
    use HasFactory;

    protected $table = 'clear_advance_form_details';
    protected $fillable = [
        'req_recid',
        'description',
        'invoice_number',
        'department_code',
        'budget_code',
        'alternative_budget_code',
        'exchange_rate_khr',
        'unit',
        'quantity',
        'unit_price_usd',
        'unit_price_khr',
        'total_amount_usd',
        'total_amount_khr',
        'total_budget_amount',
        'total_budget_amount_used',
        'total_budget_ytd_expense_amount',
        'total_alt_budget_amount',
        'total_alt_budget_amount_used',
        'total_alt_budget_ytd_expense_amount',
        'within_budget',
        'advance_form_detail_id',
        'old_payment_remaining',
        'vat_item',
        'vat_item_khr'
    ];
}


