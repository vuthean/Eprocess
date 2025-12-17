<?php

namespace Database\Seeders;

use App\Models\Formname;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddFormnameBankReceiptSeender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formname = [[
            'formname' => 'BankReceiptVourcherRequest',
            'description' => 'form/bank-receipt-vouchers/detail',
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
