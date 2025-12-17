<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Blamable;

class CashReceiptVoucherFlowconfig extends Model
{
    use HasFactory;
    use Blamable;
    protected $table    = 'cash_receipt_voucher_flow_configs';

    protected $fillable = [
        'min_amount',
        'step',
        'group_id',
        'checker'
    ];
}
