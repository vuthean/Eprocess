<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewBankPaymentVoucherReference extends Model
{
    use HasFactory;
    protected $fillable = [
        'req_recid',
    ];
}
