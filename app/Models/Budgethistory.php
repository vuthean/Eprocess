<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budgethistory extends Model
{
    use HasFactory;
    protected $table = 'budgethistory';
    protected $fillable = [
        'req_recid',
        'budget_code',
        'alternative_budget_code',
        'budget_amount_use',
        'alternative_amount_use',
        'budget_over_limit',
        'alternative_over_limit'
    ];
}
