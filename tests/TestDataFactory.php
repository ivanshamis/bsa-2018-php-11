<?php

namespace Tests;

use App\Entity\{Currency,Lot,Wallet,Money};
use App\User;

class TestDataFactory
{
    public static function createUser(): User
    {
        return factory(User::class)->make([
            'is_admin' => false
        ]);
    }

    public static function createCurrency(): Currency
    {
        return factory(Currency::class)->make();
    }

    public static function createAdminUser(): User
    {
        return factory(User::class)->make([
            'is_admin' => true
        ]);
    }

    public static function createLot(): Lot
    {
        return factory(Lot::class)->make();
    }

    public static function createWallet(): Wallet
    {
        return factory(Wallet::class)->make();
    }

    public static function createMoney(): Money
    {
        return factory(Money::class)->make();
    }
}