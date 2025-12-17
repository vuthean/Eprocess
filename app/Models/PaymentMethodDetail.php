<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodDetail extends Model
{
    use HasFactory;
    use Blamable;

    protected $table = 'payment_method_details';
    protected $fillable = [
        'payment_method_id',
        'group_id'
    ];
}
