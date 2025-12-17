<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Formname;

class AddFormnameCashReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formname = [[
            'formname' => 'CashReceiptVourcherRequest',
            'description' => 'form/cash-receipt-vouchers/detail',
        ]];

        DB::transaction(function() use($formname){
            foreach($formname as $activity){
                $isExist = Formname::firstWhere('formname',$activity['description']);
                if(!$isExist){
                    Formname::create($activity);
                }
            }
        });
    }
}
