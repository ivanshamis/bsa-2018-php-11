<?php

namespace App\Response;

use App\Entity\{Lot, User, Currency, Money, Wallet};
use App\Response\Contracts\LotResponse;

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
            int $id,
            string $userName,
            string $currencyName, 
            float $amount,
            int $dateTimeOpen,
            int $dateTimeClose,
            float $price)   
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->currencyName = $currencyName;
        $this->amount = $amount;
        $this->dateTimeOpen = date('Y/m/d h:i:s', $dateTimeOpen);
        $this->dateTimeClose = date('Y/m/d h:i:s', $dateTimeClose);
        $this->price = number_format($price,2,",","");
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