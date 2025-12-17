<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emailgroup extends Model
{
    use HasFactory;
    protected $table = 'emailgroup';
    protected $fillable = [
        'group_id',
        'group_name',
        'group_email'
    ];    
}
