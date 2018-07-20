<?php

namespace App\Request;

use App\Request\Contracts\AddCurrencyRequest;

class AddCurrencyReq implements AddCurrencyRequest
{
    private $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }
}