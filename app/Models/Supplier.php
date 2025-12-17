<?php

namespace App\Models;

use App\Traits\Blamable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    use Blamable;
    
    protected $table = 'suppliers';
    protected $fillable = [
        'code',
        'first_name_eng',
        'last_name_eng',
        'first_name_kh',
        'last_name_kh',
        'full_name_eng',
        'full_name_kh',
        'gender',
        'date_of_birth',
        'race',
        'nationality',
        'id_card_number',
        'passport_number',
        'phone_number',
        'email',
        'address',
        'type',
        'acct_name',
        'acct_number',
        'acct_currency',
        'pay_to_bank',
    ];

    
}


