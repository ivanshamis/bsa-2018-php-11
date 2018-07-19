<?php

namespace App\Repository;

use App\Repository\Contracts\MoneyRepository;
use App\Entity\Money;

class MoneyRepo implements MoneyRepository
{
    public function save(Money $money) : Money
    {
        $money->save();
        return $money;
    }

    public function findByWalletAndCurrency(int $walletId, int $currencyId) : ?Money
    {
        $money = Money::where('wallet_id', $walletId)->where('currency_id', $currencyId)->first();    
    }
}
