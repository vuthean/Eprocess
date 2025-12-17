<?php

namespace App\Enums;

class FormTypeEnum
{
    public static function ProcurementRequest(){
        return '1';
    }

    public static function PaymentRequest(){
        return '2';
    }

    public static function AdvanceFormRequest(){
        return '3';
    }

    public static function ClearAdvanceFormRequest(){
        return '4';
    }

    public static function BankPaymentVourcherRequest(){
        return '5';
    }
    public static function JournalVourcherRequest(){
        return '6';
    }
    public static function BankReceiptVourcherRequest(){
        return '7';
    }
    public static function CashPaymentVourcherRequest(){
        return '8';
    }
    public static function CashReceiptVourcherRequest(){
        return '9';
    }
    public static function BankVourcherRequest(){
        return '10';
    }
}
