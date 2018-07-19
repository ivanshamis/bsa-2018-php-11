<?php

namespace App\Service;

use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;
use App\Service\Contracts\CurrencyService;
use App\Repository\Contracts\CurrencyRepository;

class CurrencyServ implements CurrencyService
{
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function addCurrency(AddCurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency;
        $currency->fill(['name' => $currencyRequest->getName()]); 
        return $this->currencyRepository->add($currency);
    }
}