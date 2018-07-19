<?php

namespace App\Request;

use App\Request\Contracts\AddLotRequest;

class AddLotReq implements AddLotRequest
{
    private $currencyId;
    private $sellerId;
    private $dateTimeOpen;
    private $dateTimeClose;
    private $price;

    public function __construct(
        int $currencyId,
        int $sellerId,
        int $dateTimeOpen,
        int $dateTimeClose,
        float $price
    ) 
    {
        $this->currencyId = $currencyId;
        $this->sellerId = $sellerId;
        $this->dateTimeOpen = $dateTimeOpen;
        $this->dateTimeClose = $dateTimeClose;
        $this->price = $price;
    }

    public function getCurrencyId() : int
    {
        return $this->currencyId;
    }

    /**
     * An identifier of user
     *
     * @return int
     */
    public function getSellerId() : int
    {
        return $this->sellerId;
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeOpen() : int
    {
        return $this->dateTimeOpen;
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeClose() : int
    {
        return $this->dateTimeClose;
    }

    public function getPrice() : float
    {
        return $this->price;
    }
}