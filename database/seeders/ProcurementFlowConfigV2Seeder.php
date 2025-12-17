<?php

namespace Database\Seeders;

use App\Enums\FormTypeEnum;
use App\Models\Flowconfig;
use Illuminate\Database\Seeder;

class ProcurementFlowConfigV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flowConfigures = config('procurementFlowConfig');
        foreach ($flowConfigures as $flow) {
            $isExist = Flowconfig::where('step_number', $flow['step_number'])
                ->where('checker', $flow['checker'])
                ->where('within_budget', $flow['within_budget'])
                ->where('amount_request', $flow['amount_request'])
                ->where('version', $flow['version'])
                ->where('approver_is_ceo', $flow['approver_is_ceo'])
                ->where('req_name', $flow['req_name'])
                ->first();
            if (!$isExist) {
                Flowconfig::create($flow);
            }
        }
    }
}
