<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rolemgt extends Model
{
    use HasFactory;
    protected $table = 'rolemgt';
    protected $fillable = [
        'role_code',
        'role_name',
        'role_description'
    ];
}
