<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocateProduct extends Model
{
    use HasFactory;
    use Blamable;

    protected $fillable = [
        'req_recid',
        'general',
        'loan_general',
        'mortgage',
        'business',
        'personal',
        'card_general',
        'debit_card',
        'credit_card',
        'trade_general',
        'bank_general',
        'letter_of_credit',
        'deposit_general',
        'casa_individual',
        'td_individual',
        'casa_corporate',
        'td_corporate',
    ];

    public function getGeneralAttribute($value)
    {
        return (int)$value;
    }
    public function getLoanGeneralAttribute($value)
    {
        return (int)$value;
    }
    public function getMortgageAttribute($value)
    {
        return (int)$value;
    }
    public function getBusinessAttribute($value)
    {
        return (int)$value;
    }
    public function getPersonalAttribute($value)
    {
        return (int)$value;
    }
    public function getCardGeneralAttribute($value)
    {
        return (int)$value;
    }
    // 'debit_card',
    public function getDebitCardAttribute($value)
    {
        return (int)$value;
    }
    // 'credit_card',
    public function getCreditCardAttribute($value)
    {
        return (int)$value;
    }
    // 'trade_general',
    public function getTradeGeneralAttribute($value)
    {
        return (int)$value;
    }
    // 'bank_general',
    public function getBankGeneralAttribute($value)
    {
        return (int)$value;
    }
    // 'letter_of_credit',
    public function getLetterOfCreditAttribute($value)
    {
        return (int)$value;
    }
    // 'deposit_general',
    public function getDepositGeneralAttribute($value)
    {
        return (int)$value;
    }
    // 'casa_individual',
    public function getCasaIndividualAttribute($value)
    {
        return (int)$value;
    }
    // 'td_individual',
    public function getTdIndividualAttribute($value)
    {
        return (int)$value;
    }
    // 'casa_corporate',
    public function getCasaCorporateAttribute($value)
    {
        return (int)$value;
    }
    // 'td_corporate',
    public function getTdCorporateAttribute($value)
    {
        return (int)$value;
    }
}
