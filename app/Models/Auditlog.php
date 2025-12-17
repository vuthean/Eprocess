<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditlog extends Model
{
    use HasFactory;
    protected $table = 'auditlog';
    protected $fillable = [
        'req_recid',
        'doer_email',
        'doer_name',
        'doer_branch',
        'doer_position',
        'activity_code',
        'activity_description',
        'activity_form',
        'activity_datetime',
        'step_action',
        'doer_role',
        'doer_action'
    ];
}
