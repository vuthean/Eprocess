<?php

namespace Database\Seeders;

use App\Models\BankPaymentVoucherFlowConfig;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Http\FormRequest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FormRequestSeeder::class);
        $this->call(ProcurementFlowConfigV2Seeder::class);
        $this->call(AdvanceFormFlowConfigV2Seeder::class);
        $this->call(ClearAdvanceFormFlowConfigV2Seeder::class);
        $this->call(ActivityCodeSeeder::class);
        $this->call(BankPaymentVoucherFlowConfigSeeder::class);
    }
}
