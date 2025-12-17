<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Reviewapprove extends Model
{
    use HasFactory;
    protected $table = 'reviewapprove';
    protected $fillable = [
        'req_recid',
        'review',
        'second_review',
        'third_review',
        'budget_owner',
        'approve',
        'final',
        'co_approver',
        'fourth_reviewer',
        'final_group',
        'dceo_approve',
        'accounting',
        'procurement'
    ];


    public function hasApproverAsCEO()
    {
        $groupId = Groupid::where('group_id', 'GROUP_CEO')
            ->where('email', $this->approve)
            ->where('status',1)
            ->first();
        return $groupId;
    }
    public function hasApproverAsDCEO()
    {
        $groupId = Groupid::where('group_id', 'GROUP_DCEO')
            ->where('email', $this->approve)
            ->first();
        return $groupId;
    }
}
