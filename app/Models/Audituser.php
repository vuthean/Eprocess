<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audituser extends Model
{
    use HasFactory;
    protected $table = 'audituser';
    protected $fillable = [
        'doer_email',
        'doer_name',
        'doer_branch',
        'doer_position',
        'activity_code',
        'activity_description',
        'activity_form',
        'activity_datetime',
        'old_value',
        'new_value'
    ];

    protected $casts =[
        'old_value'=>'array',
        'new_value'=>'array',
    ];

    // public function getOldValueAttribute($value){
    //     return json_encode($value);
    // }

    // public function getNewValueAttribute($value){
    //     return json_encode($value);
    // }

    public function getActivityDatetimeAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y  g:i A');
    }

    public function user(){
        return $this->belongsTo(User::class,'doer_email','email');
    }
}
