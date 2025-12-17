<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerAd extends Model
{
    use HasFactory;
    protected $table = 'server_ad';
    protected $fillable = [
        'name',
        'ip',
        'port',
        'status'
    ];  
}
