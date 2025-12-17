<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requester extends Model
{
    use HasFactory;
    protected $table = 'requester';
    protected $fillable = [
        'req_recid',
        'req_email',
        'req_name',
        'req_branch',
        'req_position',
        'req_from',
        'req_date',
        'due_expect_date',
        'subject',
        'ref',
        'ccy'
    ];
}
