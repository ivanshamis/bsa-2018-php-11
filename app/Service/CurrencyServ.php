<?php

namespace App\Service;

use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;
use App\Service\Contracts\CurrencyService;
use App\Repository\Contracts\CurrencyRepository;

class CurrencyServ implements CurrencyService
{
    public function addCurrency(
        AddCurrencyRequest $currencyRequest, 
        CurrencyRepository $currencyRepository
    ): Currency
    {
        $currency = new Currency;
        $currency->fill(['name' => $currencyRequest->getName()]); 
        return $currencyRepository->add($currency);
    }
}