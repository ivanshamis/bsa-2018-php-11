<?php

namespace App\Repository;

use App\Repository\Contracts\MoneyRepository;
use App\Entity\Money;

class MoneyRepo implements MoneyRepository
{
    public function save(Money $money) : Money
    {

    }

    public function findByWalletAndCurrency(int $walletId, int $currencyId) : ?Money
    {

    }
}
