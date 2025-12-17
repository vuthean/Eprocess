<?php

namespace App\Enums;

class BudgetEnum
{
    public static function WithinBudget()
    {
        return 'Y';
    }

    public static function WithinBudgetDescription()
    {
        return 'YES';
    }

    public static function NotWithinBudget()
    {
        return 'N';
    }
    
    public static function NotWithinBudgetDescription()
    {
        return 'NO';
    }
}
