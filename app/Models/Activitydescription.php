<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activitydescription extends Model
{
    use HasFactory;
    protected $table = 'activitydescription';
    protected $fillable = [
        'activity_code',
        'activity_type',
        'activity_description'        
    ];
}
