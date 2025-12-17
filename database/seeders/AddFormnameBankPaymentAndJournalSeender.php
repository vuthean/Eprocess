<?php

namespace Database\Seeders;

use App\Models\Formname;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddFormnameBankPaymentAndJournalSeender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formname = [[
            'formname' => 'BankPaymentVourcherRequest',
            'description' => 'form/bank-payment-vouchers/detail',
        ],[
            'formname' => 'JournalVourcherRequest',
            'description' => 'form/journal-vouchers/detail',
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
