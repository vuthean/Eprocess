<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usermgt extends Model
{
    use HasFactory;
    protected $table = 'usermgt';
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'userid',
        'division',
        'department',
        'position',
        'mobile',
        'status',
        'groupid',
        'lastlogin'
    ];

    function groupid() {
        return $this->hasMany('App\Models\Groupid');
    }
}
