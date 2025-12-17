<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';
    protected $fillable = [        
        'ticket_number',
        'cif_number',
        'customer_name',
        'phone_number',
        'status'
    ];
}
