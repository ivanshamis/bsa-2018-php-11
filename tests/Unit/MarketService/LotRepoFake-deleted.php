<?php

namespace Tests\Unit\MarketService;

use App\Repository\Contracts\LotRepository;
use App\Entity\{Lot,Wallet};

class LotRepoFake implements LotRepository
{
    private $lots;

    public function add(Lot $lot) : Lot
    {   
        if (!is_array($this->lots)) {
            $lot->id = 1;
        }
        else {
            $lot->id = max(array_keys($this->lots))+1;
        }
        $this->lots[$lot->id] = $lot; 
        return $lot;
    }

    public function getById(int $id) : ?Lot
    {
        return $this->lots[$id];
    }

    /**
     * @return Lot[]
     */
    public function findAll()
    {
        return $this->lots;
    }

    public function findActiveLot(int $userId) : ?Lot
    {
        $activeLot = NULL;
        foreach ($this->lots as $lot) {
            if (($lot->user_id==$userId) and ($lot->date_time_close>time())) {
                $activeLot = $lot;
                break;
            }
        }
        return $activeLot;
    }

    public function findActiveCurrencyLot(int $userId, int $currencyId) : ?Lot
    {
        $activeLot = NULL;
        foreach ($this->lots as $lot) {
            if (
                ($lot->user_id==$userId) and 
                ($lot->getDateTimeClose()>time()) and
                ($lot->currency_id==$currencyId)
            ) {
                echo('FOUND: '.$lot->getDateTimeClose()." > ".time());
                $activeLot = $lot;
                break;
            }
        }
        if ($activeLot===NULL) {
            echo("not found {$userId} {$currencyId}".PHP_EOL);
        }
        else {
            echo("FOUND {$userId} {$currencyId}".PHP_EOL);
        }
        return $activeLot;
    }
}