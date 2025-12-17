<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Budgetcode extends Model
{
    use HasFactory;
    protected $table = 'budgetdetail';
    protected $fillable = [
        // 'branch_code',
        'budget_code',
        'budget_item',
        'budget_owner',
        'budget_name',
        'total',
        'procurement',
        'temp',
        'temp_payment',
        'remaining',
        'payment',
        'payment_remaining',
        'year',
        'modify',
        'modify_by',
        'modify_date',
        'budget_after_calculate_pr'
    ];

    protected $casts  = [
        'payment'           => 'float',
        'temp_payment'      => 'float',
        'payment_remaining' => 'float',
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
            'activity_description' => "deleted budget code ({$this->budget_code})",
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
            'activity_description' => "updated budget code ({$this->budget_code})",
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
            'activity_description' => "created budget code ({$this->budget_code})",
            'activity_form'        => class_basename($this),
            'activity_datetime'    => now(),
            'old_value'            => $this->toArray(),
            'new_value'            => $this->toArray(),
        ]);
        return true;
    }

    public function isAlreadyInUsed()
    {
        /** find budget code in payment */
        $paymentBody = Paymentbody::firstWhere('budget_code', $this->budget_code);
        if ($paymentBody) {
            return true;
        }

        /** find budget code in procurement */
        $procurementBody = Procurementbody::firstWhere('budget_code', $this->budget_code);
        if ($procurementBody) {
            return true;
        }

        return false;
    }

    public function logCollectionData($newData,$oldData)
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
            'activity_description' => "created budget code ({$this->budget_code})",
            'activity_form'        => 'upload_budget_code',
            'activity_datetime'    => now(),
            'old_value'            => $oldData,
            'new_value'            => $newData,
        ]);
        return true;
    }
}
