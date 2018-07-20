<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;
use App\Service\Contracts\WalletService;
use App\Repository\Contracts\{WalletRepository,MoneyRepository};

class WalletServ implements WalletService
{
    private $walletRepository;
    private $moneyRepository;

    public function __construct(WalletRepository $walletRepository, MoneyRepository $moneyRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
    }

    /**
     * Add wallet to user.
     *
     * @param CreateWalletRequest $walletRequest
     * @return Wallet
     */
    public function addWallet(CreateWalletRequest $walletRequest) : Wallet
    {
        $wallet = new Wallet;
        $wallet->fill([
            'user_id' => $walletRequest->getUserId()
        ]);    
        return $this->walletRepository->add($wallet);
    }

    /**
     * Add money to a wallet.
     *
     * @return Money
     */
    public function addMoney(MoneyRequest $moneyRequest) : Money
    {
        $money  = $this->moneyRepository->findByWalletAndCurrency(
            $moneyRequest->getWalletId(), 
            $moneyRequest->getCurrencyId()
        );
        if ($money === NULL) {
            $money = new Money;
            $money->fill([
                'wallet_id' => $moneyRequest->getWalletId(),
                'currency_id' => $moneyRequest->getCurrencyId(),
                'amount' => 0
            ]);
        }
        $money->amount += $moneyRequest->getAmount();
        return $this->moneyRepository->save($money);   
    }

    /**
     * Take money from a wallet.
     *
     * @param MoneyRequest $currencyRequest
     * @return Money
     */
    public function takeMoney(MoneyRequest $moneyRequest) : Money
    {
        $money  = $this->moneyRepository->findByWalletAndCurrency(
            $moneyRequest->getWalletId(), 
            $moneyRequest->getCurrencyId()
        );
        if ($money === NULL) {
            $money = new Money;
            $money->fill([
                'wallet_id' => $moneyRequest->getWalletId(),
                'currency_id' => $moneyRequest->getCurrencyId(),
                'amount' => 0
            ]);
        }
        if ($money->amount>=$moneyRequest->getAmount()) {
            $money->amount -= $moneyRequest->getAmount();
        }
        return $this->moneyRepository->save($money);
    }
}