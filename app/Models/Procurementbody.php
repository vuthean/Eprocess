<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurementbody extends Model
{
    use HasFactory;
    protected $table = 'procurementbody';
    protected $fillable = [
        'id',
        'req_recid',
        'description',
        'br_dep_code',
        'budget_code',
        'alternativebudget_code',
        'unit',
        'qty',
        'unit_price',
        'total_estimate',
        'unit_price_khr',
        'total_estimate_khr',
        'delivery_date',
        'total',
        'total_khr',
        'within_budget_code',
        'inv_no',
        'used_by_request',
        'vat',
    ];
    public function isWithinBudget(){
        return $this->within_budget_code == 'Y';
    }
}
