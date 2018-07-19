<?php

namespace App\Repository;

use App\Repository\Contracts\CurrencyRepository;
use App\Entity\Currency;

class CurrencyRepo implements CurrencyRepository
{
    public function add(Currency $currency) : Currency
    {
        $currency->save();
        return $currency;
    }

    public function getById(int $id) : ?Currency
    {
        return Currency::find($id);
    }

    public function getCurrencyByName(string $name) : ?Currency
    {
        return Currency::where('name',$name)->get();
    }

    /**
     * @return Currency[]
     */
    public function findAll()
    {
        return Currency::all();
    }
}