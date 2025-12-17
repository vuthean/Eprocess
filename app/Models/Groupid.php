<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Groupid extends Model
{
    use HasFactory;
    protected $table = 'groupid';
    protected $fillable = [
        'login_id',
        'email',
        'group_id',
        'role_id',
        'budget',
        'status',
        'is_cfo'
    ];

    public function groupDescription(){
        return $this->hasOne(Groupdescription::class,'group_id','group_id');
    }

    function usermgt() {
	    return $this->belongsTo('App\Models\usermgt');
	}

    public function auditUser()
    {
        return $this->morphMany(Audituser::class, 'model');
    }

    public function logDelete()
    {
        /**@var User $user*/
        $user             = Auth::user();
        $groupDescription = $user->groupDescription();

        $this->auditUser()->create([
            'doer_email'           => $user->email,
            'doer_name'            => "{$user->firstname} {$user->lastname}",
            'doer_branch'          => 'N/A',
            'doer_position'        => $groupDescription ? $groupDescription->group_name : 'N/A',
            'activity_code'        => 'deleted',
            'activity_description' => "delete group id ({$this->group_id})",
            'activity_form'        => class_basename($this),
            'activity_datetime'    => now(),
            'old_value'            => $this->toArray(),
            'new_value'            => $this->toArray(),
        ]);
        return true;
    }

    public function logUpdate($old)
    {
        /**@var User $user*/
        $user             = Auth::user();
        $groupDescription = $user->groupDescription();

        $this->auditUser()->create([
            'doer_email'           => $user->email,
            'doer_name'            => "{$user->firstname} {$user->lastname}",
            'doer_branch'          => 'N/A',
            'doer_position'        => $groupDescription ? $groupDescription->group_name : 'N/A',
            'activity_code'        => 'updated',
            'activity_description' => "updated group id ({$this->group_id})",
            'activity_form'        => class_basename($this),
            'activity_datetime'    => now(),
            'old_value'            => $old->toArray(),
            'new_value'            => $this->toArray(),
        ]);
        return true;
    }

    public function logNew()
    {
        /**@var User $user*/
        $user             = Auth::user();
        $groupDescription = $user->groupDescription();

        $this->auditUser()->create([
            'doer_email'           => $user->email,
            'doer_name'            => "{$user->firstname} {$user->lastname}",
            'doer_branch'          => 'N/A',
            'doer_position'        => $groupDescription ? $groupDescription->group_name : 'N/A',
            'activity_code'        => 'created',
            'activity_description' => "created group id ({$this->group_id})",
            'activity_form'        => class_basename($this),
            'activity_datetime'    => now(),
            'old_value'            => $this->toArray(),
            'new_value'            => $this->toArray(),
        ]);
        return true;
    }
    public function findEmailUnderCDO(){
        $emails = Groupid::where('group_id','GROUP_UNDER_CDO')->where('status',1)->get();
        
        $email_under_cdo = [];
        foreach($emails as $email){
            array_push($email_under_cdo,$email->email);
        }
        return $email_under_cdo;
    }
    public function findEmailUnderDCEO(){
        $emails = Groupid::where('group_id','GROUP_UNDER_DCEO')->where('status',1)->get();
        
        $email_under_dceo = [];
        foreach($emails as $email){
            array_push($email_under_dceo,$email->email);
        }
        return $email_under_dceo;
    }
}
