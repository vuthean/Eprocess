<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'action',
        'model',
        'description',
        'request_body',
        'old_data',
        'new_data',
        'proceeded_at',
    ];

    protected $casts =[
        'request_body'=>'array',
        'old_data'=>'array',
        'new_data'=>'array',
    ];

    public function getOldDataAttribute($value){
        return json_encode($value);
    }

    public function getNewDataAttribute($value){
        return json_encode($value);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
