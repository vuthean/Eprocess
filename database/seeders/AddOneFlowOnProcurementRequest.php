<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flowconfig;
use Illuminate\Support\Facades\DB;

class AddOneFlowOnProcurementRequest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flowConfig = [[
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '1',
            'notification_type'=>'Procurement',
            'step_description'=>'first_reviewer',
            'version' => '2',
            'checker' => 'first_reviewer',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0',

        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '2',
            'notification_type'=>'Procurement',
            'step_description'=>'second_reviewer',
            'version' => '2',
            'checker' => 'second_reviewer',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '3',
            'notification_type'=>'Procurement',
            'step_description'=>'budget_owner',
            'version' => '2',
            'checker' => 'budget_owner',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '4',
            'notification_type'=>'Procurement',
            'step_description'=>'approver',
            'version' => '2',
            'checker' => 'approver',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '5',
            'notification_type'=>'Procurement',
            'step_description'=>'co_approver',
            'version' => '2',
            'checker' => 'co_approver',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '6',
            'notification_type'=>'Procurement',
            'step_description'=>'receiver',
            'version' => '2',
            'checker' => 'receiver',
            'approver_is_ceo'=>'0',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '5',
            'notification_type'=>'Procurement',
            'step_description'=>'approver_ceo',
            'version' => '2',
            'checker' => 'approver_ceo',
            'approver_is_ceo'=>'1',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ],
        [
            'req_name'=>'1',
            'within_budget'=>'Y',
            'amount_request'=>'<=3000',
            'step_number' => '6',
            'notification_type'=>'Procurement',
            'step_description'=>'receiver',
            'version' => '2',
            'checker' => 'receiver',
            'approver_is_ceo'=>'1',
            'request_is_sole_source'=>'Y',
            'is_accounting_team'=>'0'
        ]];
        DB::transaction(function() use($flowConfig){
            foreach($flowConfig as $activity){
                Flowconfig::create($activity);
            }
        });
    }
}
