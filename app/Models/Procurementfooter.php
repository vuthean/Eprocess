<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurementfooter extends Model
{
    use HasFactory;
    protected $table = 'procurementfooter';
    protected $fillable = [
        'req_recid',
        'vender_name',
        'description'    
    ];    
}
