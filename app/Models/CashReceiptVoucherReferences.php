<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReceiptVoucherReferences extends Model
{
    use HasFactory;
    protected $table = 'view_cash_receipt_voucher_references';
    protected $fillable = [
        'req_recid',
    ];
}
