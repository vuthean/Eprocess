<?php

namespace App\Traits;

trait Currency
{
    public function currentExchangeRate()
    {
        return 4000;
    }

    public function getUSDAmount($amount,$currency)
    {
        $exchangeRate = $this->currentExchangeRate();
        if($currency == 'KHR'){
            $totalAmount = floatval($amount);
            return $totalAmount/$exchangeRate;
        }

        return floatval($amount);
    }

    public function getKHRAmount($amount,$currency)
    {
        $exchangeRate = $this->currentExchangeRate();
        if($currency == 'USD'){
            $totalAmount = floatval($amount);
            return $totalAmount*$exchangeRate;
        }

        return floatval($amount);
    }
}
