<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerCode extends Model
{
    use HasFactory;
    use Blamable;

    protected $table = 'general_ledger_codes';
    protected $fillable = [
        'account_number',
        'account_name'
    ];
}
