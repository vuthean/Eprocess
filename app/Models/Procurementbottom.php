<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurementbottom extends Model
{
    use HasFactory;
    protected $table = 'procurementbottom';
    protected $fillable = [
        'req_recid',
        'general',
        'loan_general',
        'mortage',
        'busines',
        'personal',
        'card_general',
        'debit_card',
        'credit_card',
        'trade_general',
        'bank_guarantee',
        'letter_of_credit',
        'deposit_general',
        'casa_individual',
        'td_individual',
        'casa_corporate',
        'sagement_general', 
        'sagement_bfs', 
        'sagement_rfs',
        'sagement_pb',
        'sagement_pcp',
        'sagement_afs',
        'remarks'   
    ];
}
