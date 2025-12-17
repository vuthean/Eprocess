<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentCode extends Model
{
    use HasFactory;
    use Blamable;
    
    protected $table = 'segment_codes';
    protected $fillable = [
        'code',
        'type',
        'description',
    ];
}