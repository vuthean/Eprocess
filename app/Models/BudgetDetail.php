<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDetail extends Model
{
    use HasFactory;

    protected $table = 'budgetdetail';
    protected $fillable = [
        'branch_code',
        'budget_code',
        'budget_item',
        'budget_owner',
        'budget_name',
        'total',
        'procurement',
        'temp',
        'temp_payment',
        'remaining',
        'payment',
        'payment_remaining',
        'year',
        'modify',
        'modify_by',
        'modify_date',
    ];
}
