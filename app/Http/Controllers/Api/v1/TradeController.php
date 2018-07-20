<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\Contracts\MarketService;

class TradeController extends Controller
{
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }


    public function store(Request $request)
    {
        //
    }
}
