<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockForm extends Model
{
    use HasFactory;
    protected $table = 'block_form';
    protected $fillable = [
        'block_day',
        'block_date'
        
    ];
}
