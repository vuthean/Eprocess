<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flowconfig extends Model
{
    use HasFactory;
    protected $table = 'flowconfig';
    protected $fillable = [
        'req_name',
        'within_budget',
        'amount_request',
        'step_number',
        'checker',
        'notification_type',        
        'step_description',
        'approver_is_ceo',
        'version',
        'is_accounting_team',
        'request_is_sole_source',
    ];
}
