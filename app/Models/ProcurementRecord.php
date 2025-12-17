<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRecord extends Model
{
    use HasFactory;
    protected $table = 'view_procurement_record_table';
    protected $fillable = [
        'req_recid',
        'req_email',
        'req_name',
        'req_brach',
        'req_position',
        'req_date',
        'req_from',
        'req_type',
        'next_checker_group',
        'nex_checker_role',
        'step_number',
        'step_status',
        'req_status',
        'within_budget',
        'created_at',
        'updated_at',
        'formname',
        'description',
        'final',
        'subject',
        'status',
        'purpose',
        'bid',
        'justification',
        'comment_by_pr',
        'vat',  
        'grand_total'
    ];
}
