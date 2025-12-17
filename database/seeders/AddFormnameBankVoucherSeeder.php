<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Formname;
use App\Models\Groupdescription;

class AddFormnameBankVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formname = [[
            'formname' => 'BankVourcherRequest',
            'description' => 'form/bank-vouchers/detail',
        ]];
        $group_id = [[
            'group_id' => 'GROUP_TREASURY',
            'group_name' => 'Treasury',
            'group_description' => 'GROUP TREASURY',
            'special' => 'Y',
        ]];

        DB::transaction(function() use($formname){
            foreach($formname as $activity){
                $isExist = Formname::firstWhere('formname',$activity['formname']);
                if(!$isExist){
                    Formname::create($activity);
                }
            }
        });
        DB::transaction(function() use($group_id){
            foreach($group_id as $activity){
                $isExist = Groupdescription::firstWhere('group_name',$activity['group_name']);
                if(!$isExist){
                    Groupdescription::create($activity);
                }
            }
        });
    }
}
