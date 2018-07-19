<?php

namespace App\Response;

use App\Entity\{Lot, User, Currency, Money, Wallet};
use App\Response\Contracts\LotResponse;
use App\Repository\Contracts\{LotRepository,CurrencyRepository,UserRepository,MoneyRepository,WalletRepository};

class LotRespo implements LotResponse
{
    private $id;
    private $userName;
    private $currencyName;
    private $amount;
    private $dateTimeOpen;
    private $dateTimeClose;
    private $price;

    public function __construct(
        Lot $lot, 
        UserRepository $userRepository,
        LotRepository $lotRepository, 
        CurrencyRepository $currencyRepository,
        MoneyRepository $moneyRepository,
        WalletRepository $walletRepository
    ) 
    {
        $this->id = $lot->id;
        $this->userName = $userRepository->getById($lot->seller_id)->name;
        $this->currencyName = $currencyRepository->getById($lot->currency_id)->name;
        $wallet = $walletRepository->findByUser($lot->seller_id);
        $money = $moneyRepository->findByWalletAndCurrency($wallet->id,$lot->currency_id);
        $this->amount = $money->amount;
        $this->dateTimeOpen = date('Y/m/d h:i:s', $lot->getDateTimeOpen());
        $this->dateTimeClose = date('Y/m/d h:i:s', $lot->getDateTimeClose());
        $this->price = number_format($lot->price,2,",","");
    }

    /**
     * An identifier of lot
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    public function getUserName() : string
    {
        return $this->userName;
    }

    public function getCurrencyName() : string
    {
        return $this->currencyName;
    }

    /**
     * All amount of currency that user has in the wallet.
     *
     * @return float
     */
    public function getAmount() : float
    {
        return $this->amount;
    }

    /**
     * Format: yyyy/mm/dd hh:mm:ss
     *
     * @return string
     */
    public function getDateTimeOpen() : string
    {
        return $this->dateTimeOpen;
    }

    /**
     * Format: yyyy/mm/dd hh:mm:ss
     *
     * @return string
     */
    public function getDateTimeClose() : string
    {
        return $this->dateTimeClose;
    }

    /**
     * Price per one amount of currency.
     *
     * Format: 00,00
     *
     * @return string
     */
    public function getPrice() : string
    {
        return $this->price;
    }
}