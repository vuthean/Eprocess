<?php

namespace Database\Seeders;

use App\Models\CashReceiptVoucherFlowconfig;
use App\Models\Groupid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashReceiptVoucherFlowConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reviewers = Groupid::select('group_id')->where('role_id', 2)->groupBy('group_id')->get();
        $approvers = Groupid::select('group_id')->where('role_id', 3)->groupBy('group_id')->get();
        $lastApprovers = collect(['GROUP_ACCOUNTING','GROUP_FINANCE']);

        DB::transaction(function () use ($reviewers, $approvers, $lastApprovers) {
            $amountMethrix = [0,500.001,2500.001,5000.001];
            for ($i=0 ; $i<count($amountMethrix) ; $i++) {
                $isExist = CashReceiptVoucherFlowconfig::firstWhere('min_amount', $amountMethrix[$i]);
                if (!$isExist) {
                    /** insert first level */
                    foreach ($reviewers as $reviewer) {
                        CashReceiptVoucherFlowconfig::create([
                            'min_amount' => $amountMethrix[$i],
                            'step'       => 1,
                            'group_id'   => $reviewer->group_id,
                            'checker'    => 'first_reviewer'
                       ]);
                    }
                    /** instert second level */
                    foreach ($approvers as $approver) {
                        CashReceiptVoucherFlowconfig::create([
                            'min_amount' => $amountMethrix[$i],
                            'step'       => 2,
                            'group_id'   => $approver->group_id,
                            'checker'    => 'approver'
                       ]);
                    }
                    /** add group CFO to second level too */
                    CashReceiptVoucherFlowconfig::create([
                        'min_amount' => $amountMethrix[$i],
                        'step'       => 2,
                        'group_id'   => 'GROUP_CFO',
                        'checker'    => 'approver'
                    ]);

                    /** insert third level */
                    foreach ($lastApprovers as $lastApprover) {
                        CashReceiptVoucherFlowconfig::create([
                        'min_amount' => $amountMethrix[$i],
                        'step'       => 3,
                        'group_id'   => $lastApprover,
                        'checker'    => 'accounting'
                   ]);
                    }
                }
            }
        });
    }
}
