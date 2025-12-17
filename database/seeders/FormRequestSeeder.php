<?php

namespace Database\Seeders;

use App\Models\Formname;
use Illuminate\Database\Seeder;

class FormRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = [[
            'id'          => 1,
            'formname'    => 'Procurement Request',
            'description' => 'form/procurement/detail'
        ],[
            'id'          => 2,
            'formname'    => 'Payment Request',
            'description' => 'form/payment/detail'
        ],[
            'id'          => 3,
            'formname'    => 'AdvanceFormRequest',
            'description' => 'form/advances/detail'
        ],[
            'id'          => 4,
            'formname'    => 'ClearAdvanceFormRequest',
            'description' => 'form/clear-advances/detail'
        ],[
            'id'          => 5,
            'formname'    => 'BankPaymentVourcherRequest',
            'description' => 'form/bank-payment-vouchers/detail'
        ]];

        /**find exist form */
        foreach ($forms as $form) {
            $formRequest = (object)$form;
            $isExist = Formname::firstWhere('formname', $formRequest->formname);
            if (!$isExist) {
                Formname::create([
                    'formname'    => $formRequest->formname,
                    'description' => $formRequest->description
                ]);
            }
        }
    }
}
