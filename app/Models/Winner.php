<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;
    protected $table = 'winner';
    protected $fillable = [        
        'ticket_number',
        'cif_number',
        'customer_name',
        'phone_number',
        'prize',
        'spinby',
        'datetime_spin'
    ];
}
