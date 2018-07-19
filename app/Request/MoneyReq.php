<?php

namespace App\Request;

use App\Request\Contracts\MoneyRequest;

class MoneyReq implements MoneyRequest
{
    private $walletId;
    private $currencyId;
    private $amount;

    public function setWalletId(int $walletId)
    {
        $this->walletId = $walletId;
    }

    public function setCurrencyId(int $currencyId)
    {
        $this->currencyId = $currencyId;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    public function getWalletId() : int
    {
        return $this->walletId;
    }

    public function getCurrencyId() : int
    {
        return $this->currencyId;
    }

    public function getAmount() : float
    {
        return $this->amount;
    }
}