<?php

namespace App\Repository;

use App\Repository\Contracts\CurrencyRepository;
use App\Entity\Currency;

class CurrencyRepo implements CurrencyRepository
{
    public function add(Currency $currency) : Currency
    {

    }

    public function getById(int $id) : ?Currency
    {

    }

    public function getCurrencyByName(string $name) : ?Currency
    {

    }

    /**
     * @return Currency[]
     */
    public function findAll()
    {
        
    }
}