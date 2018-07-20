<?php

namespace App\Repository;

use App\Repository\Contracts\WalletRepository;
use App\Entity\Wallet;

class WalletRepo implements WalletRepository
{
    public function add(Wallet $wallet) : Wallet
    {
        $wallet->save();
        return $wallet;
    }

    public function findByUser(int $userId) : ?Wallet
    {
        return Wallet::where('user_id',$userId)->first();
    }
}