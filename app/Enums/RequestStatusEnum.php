<?php

namespace App\Enums;

class RequestStatusEnum
{
    public static function Save()
    {
        return '001';
    }
    public static function Pending()
    {
        return '002';
    }
    public static function Approve()
    {
        return '003';
    }
    public static function Rejected()
    {
        return '004';
    }
    public static function Approved()
    {
        return '005';
    }
    public static function Query()
    {
        return '006';
    }
}
