<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentbody extends Model
{
    use HasFactory;
    protected $table = 'paymentbody';
    protected $fillable = [
        'req_recid',
        'inv_no',
        'description',
        'br_dep_code',
        'budget_code',
        'alternativebudget_code',
        'unit',
        'qty',
        'unit_price',
        'total',
        'ytd_expense',
        'total_budget',
        'sub_total',
        'discount',
        'vat',
        'wht',
        'deposit',
        'net_payable',
        'vat_khr',
        'wht_khr',
        'deposit_khr',
        'net_payable_khr',
        'used_by_request',
        'old_payment_remaining',
        'vat_item_khr',
        'vat_item'
    ];
    protected $casts  = [
        'ytd_expense'=> 'float',
    ];
}
