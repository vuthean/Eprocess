<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPaymentReceiptVoucherReference extends Model
{
    use HasFactory;
    protected $table = 'view_cash_payment_voucher_references';
    protected $fillable = [
        'req_recid',
    ];
}
