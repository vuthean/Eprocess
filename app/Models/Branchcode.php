<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Branchcode extends Model
{
    use HasFactory;
    protected $table    = 'branchcode';
    protected $fillable = [
        'branch_code',
        'branch_name',
        'ceated_at',
    ];

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
            'activity_description' => "deleted branch code ({$this->branch_code})",
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
            'activity_description' => "updated branch code ({$this->branch_code})",
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
            'activity_description' => "create new branch code ({$this->branch_code})",
            'activity_form'        => class_basename($this),
            'activity_datetime'    => now(),
            'old_value'            => $this->toArray(),
            'new_value'            => $this->toArray(),
        ]);
        return true;
    }
}
