<?php

namespace Database\Seeders;

use App\Models\Flowconfig;
use Illuminate\Database\Seeder;

class ClearAdvanceFormFlowConfigV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flowConfigures = config('clearAdvanceFlowConfig');
        foreach ($flowConfigures as $flow) {
            $isExist = Flowconfig::where('step_number', $flow['step_number'])
                ->where('checker', $flow['checker'])
                ->where('within_budget', $flow['within_budget'])
                ->where('amount_request', $flow['amount_request'])
                ->where('version', $flow['version'])
                ->where('approver_is_ceo', $flow['approver_is_ceo'])
                ->where('is_accounting_team', $flow['is_accounting_team'])
                ->where('req_name', $flow['req_name'])
                ->first();
            if (!$isExist) {
                Flowconfig::create($flow);
            }
        }
    }
}
