<?php

namespace App\Repository;

use App\Repository\Contracts\LotRepository;
use App\Entity\{Lot,Wallet};

class LotRepo implements LotRepository
{
    public function add(Lot $lot) : Lot
    {
        $lot->save();
        return $lot;
    }

    public function getById(int $id) : ?Lot
    {
        return Lot::find($id);
    }

    /**
     * @return Lot[]
     */
    public function findAll()
    {
        return Lot::all();
    }

    public function findActiveLot(int $userId) : ?Lot
    {
        return Lot::where('seller_id',$userId)->where('date_time_close','>',time())->first();    
    }

    public function findActiveCurrencyLot(int $userId, int $currencyId) : ?Lot
    {
        return Lot::where('seller_id', $userId)
            ->where('currency_id', $currencyId)
            ->where('date_time_close','>',time())
            ->first();    
    }
}