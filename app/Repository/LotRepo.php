<?php

namespace App\Repository;

use App\Repository\Contracts\LotRepository;
use App\Entity\Lot;

class LotRepo implements LotRepository
{
    public function add(Lot $lot) : Lot
    {

    }

    public function getById(int $id) : ?Lot
    {

    }

    /**
     * @return Lot[]
     */
    public function findAll()
    {

    }

    public function findActiveLot(int $userId) : ?Lot
    {
        
    }
}