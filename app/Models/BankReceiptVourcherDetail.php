<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Blamable;

class BankReceiptVourcherDetail extends Model
{
    use HasFactory;
    use Blamable;

    protected $table    = 'bank_receipt_voucher_details';
    protected $fillable = [
        'req_recid',
        'gl_code',
        'account_name',
        'branch_code',
        'currency',
        'dr_cr',
        'amount',
        'lcy_amount',
        'budget_code',
        'al_budget_code',
        'tax_code',
        'supp_code',
        'department_code',
        'product_code',
        'segment_code',
        'naratives',
        'reference_req_recid',
        'reference_item_id',
    ];
}
