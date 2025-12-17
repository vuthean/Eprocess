<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankVoucherFlowConfig extends Model
{
    use HasFactory;
    use Blamable;
    protected $table    = 'bank_voucher_flow_configs';

    protected $fillable = [
        'min_amount',
        'step',
        'group_id',
        'checker'
    ];
}
