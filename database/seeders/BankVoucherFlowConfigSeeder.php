<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankVoucherFlowConfig;
use Illuminate\Support\Facades\DB;

class BankVoucherFlowConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flowConfig = [[
            'step' => '1',
            'group_id' => 'GROUP_TREASURY',
            'checker' => 'first_reviewer',
        ],
        [
            'step' => '2',
            'group_id' => 'GROUP_TREASURY',
            'checker' => 'approver',
        ]];
        DB::transaction(function() use($flowConfig){
            foreach($flowConfig as $activity){
                    BankVoucherFlowConfig::create($activity);
            }
        });
    }
}
