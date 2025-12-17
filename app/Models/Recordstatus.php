<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recordstatus extends Model
{
    use HasFactory;
    protected $table = 'recordstatus';
    protected $fillable = [
        'record_status_id',
        'record_status_description'
    ];
}
