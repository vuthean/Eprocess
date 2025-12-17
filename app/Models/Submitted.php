<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submitted extends Model
{
    use HasFactory;
    protected $table = 'submitted';
    protected $fillable = [
        'req_recid',
        'req_email',
        'req_name',
        'req_branch',
        'req_position',
        'req_from',
        'req_type',
        'req_date'
    ];
}
