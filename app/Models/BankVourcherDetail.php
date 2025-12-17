<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Blamable;

class BankVourcherDetail extends Model
{
    use HasFactory;
    use Blamable;

    protected $table    = 'bank_voucher_details';
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
        'supp_code',
        'department_code',
        'descriptions',
    ];
}
