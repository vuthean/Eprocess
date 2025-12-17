<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAXCode extends Model
{
    use HasFactory;
    use Blamable;

    protected $table = 'tax_codes';
    protected $fillable = [
        'code',
        'rate',
        'gl_description',
        'name'
    ];
}
