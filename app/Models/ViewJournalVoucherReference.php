<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Blamable;

class ViewJournalVoucherReference extends Model
{
    use HasFactory;
    use Blamable;


    protected $table    = 'view_journal_voucher_references';
    use HasFactory;
    protected $fillable = [
        'req_recid',
    ];
}
