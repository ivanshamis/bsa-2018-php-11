<?php

namespace App\Service;

use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;
use App\Service\Contracts\CurrencyService;

class CurrencyServ implements CurrencyService
{
    public function addCurrency(AddCurrencyRequest $currencyRequest) : Currency
    {
        return Currency::create(
            ['name' => $currencyRequest->getName()]
        );
    }
}
