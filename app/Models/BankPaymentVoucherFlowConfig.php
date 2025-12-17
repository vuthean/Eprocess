<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPaymentVoucherFlowConfig extends Model
{
    use HasFactory;
    use Blamable;
    protected $table    = 'bank_payment_voucher_flow_configs';

    protected $fillable = [
        'min_amount',
        'step',
        'group_id',
        'checker'
    ];
}
