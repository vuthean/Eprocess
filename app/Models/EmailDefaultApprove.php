<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailDefaultApprove extends Model
{
    use HasFactory;
    protected $table = 'email_default_approve';
    protected $fillable = [
        'email',
        'level',
    ];
}
