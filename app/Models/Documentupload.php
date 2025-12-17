<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentupload extends Model
{
    use HasFactory;
    protected $table = 'documentupload';
    protected $fillable = [
        'req_recid',
        'filename',
        'filepath',
        'doer_email',
        'doer_name',
        'activity_form',
        'uuid',
        'activity_datetime',
    ];
}
