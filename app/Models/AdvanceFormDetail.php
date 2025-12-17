<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceFormDetail extends Model
{
    use HasFactory;
    use Blamable;

    protected $fillable = [
        'req_recid',
        'invoice_number',
        'description',
        'department_code',
        'unit',
        'quantity',
        'exchange_rate_khr',
        'unit_price_usd',
        'total_amount_usd',
        'unit_price_khr',
        'total_amount_khr',

        'budget_code',
        'total_budget_amount',
        'total_budget_amount_used',
        'total_budget_ytd_expense_amount',

        'alternative_budget_code',
        'total_alt_budget_amount',
        'total_alt_budget_amount_used',
        'total_alt_budget_ytd_expense_amount',
        'within_budget',
        'procurment_body_id',
        'is_cleared',
        'used_by_request',
        'used_by_request_bank_voucher',
        'vat_item',
        'vat_item_khr',
    ];
}