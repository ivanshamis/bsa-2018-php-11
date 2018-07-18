<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;
use App\Service\Contracts\WalletService;

class WalletServ implements WalletService
{
    /**
     * Add wallet to user.
     *
     * @param CreateWalletRequest $walletRequest
     * @return Wallet
     */
    public function addWallet(CreateWalletRequest $walletRequest) : Wallet
    {
        return Wallet::create(
            ['user_id' => $walletRequest->getUserId()]
        );
    }

    public function getMoney(int $walletId, int $currencyId): Money
    {
        $money = Money::where('wallet_id', $walletId)->where('currency_id', $currencyId)->first();
        if ($money === NULL) {
            return Money::create([
                'wallet_id' => $walletId,
                'currency_id' => $currencyId,
                'amount' => 0
            ]);
        }
    }

    /**
     * Add money to a wallet.
     *
     * @return Money
     */
    public function addMoney(MoneyRequest $moneyRequest) : Money
    {
        $money = $this->getMoney($moneyRequest->getWalletId(), $moneyRequest->getCurrencyId());
        $money->amount += $moneyRequest->getAmount();
        return $money;   
    }

    /**
     * Take money from a wallet.
     *
     * @param MoneyRequest $currencyRequest
     * @return Money
     */
    public function takeMoney(MoneyRequest $moneyRequest) : Money
    {
        $money = $this->getMoney($moneyRequest->getWalletId(), $moneyRequest->getCurrencyId());
        if ($money->amount>=$moneyRequest->getAmount()) {
            $money->amount -= $moneyRequest->getAmount();
        }
        return $money;
    }

    public function WalletIdByUserId(int $userId): int
    {
        return Wallet::where('user_id', $userId)->first()->id;
    }
}