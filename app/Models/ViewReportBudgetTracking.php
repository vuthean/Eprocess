<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewReportBudgetTracking extends Model
{
    use HasFactory;
    protected $fillable = [
        'budget_code',
        'budget_item',
        'budget_owner',
        'total',
        'ytd_payment',
        'remaining_payment',
        'ytd_procurement',
        'remaining_procurement',
        'year',
        'created_at',
        'fullname',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
