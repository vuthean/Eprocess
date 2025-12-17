<?php

namespace App\Enums;

class ActivityCodeEnum
{
    public static function Submitted()
    {
        return 'A001';
    }
    public static function Approved()
    {
        return 'A002';
    }
    public static function Rejected()
    {
        return 'A003';
    }
    public static function AssignBack()
    {
        return 'A004';
    }
    public static function Resubmitted()
    {
        return 'A005';
    }
    public static function Closed()
    {
        return 'A006';
    }
    public static function Assign()
    {
        return 'A007';
    }
    public static function Query()
    {
        return 'A008';
    }
    public static function Transfer()
    {
        return 'A009';
    }
    public static function PaidYes()
    {
        return 'A010';
    }
    public static function PaidNo()
    {
        return 'A011';
    }
    public static function PaidCancel()
    {
        return 'A012';
    }
}
