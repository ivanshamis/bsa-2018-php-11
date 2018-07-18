<?php

namespace App\Repository;

use App\Repository\Contracts\WalletRepository;
use App\Entity\Wallet;

class WalletRepo implements WalletRepository
{
    public function add(Wallet $wallet) : Wallet
    {

    }

    public function findByUser(int $userId) : ?Wallet
    {

    }
}