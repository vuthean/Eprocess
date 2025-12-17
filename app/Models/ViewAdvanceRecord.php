<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewAdvanceRecord extends Model
{
    use HasFactory;
    protected $table ='view_advance_records';
    protected $fillable = [
        'request_date',
        'approval_date',
        'req_recid',
        'requester',
        'department',
        'currency',
        'request_amount_khr',
        'request_amount_usd',
        'paid_by',
        'paid',
        'paid_date',
        'cleared',
    ];

    public function getRequestDateAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('Y-m-d');
        }
    }
    public function getApprovalDateAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('Y-m-d');
        }
       
    }
    public function getPaidDateAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('Y-m-d');
        }
        
    }
}

