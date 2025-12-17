<?php


namespace App\Traits;

use App\Observers\BlamableObserver;

trait Blamable
{
    public static function boot()
    {
        parent::boot();
        static::observe(new BlamableObserver);
    }
}
