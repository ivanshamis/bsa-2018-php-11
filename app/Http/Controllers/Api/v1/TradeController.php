<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\BuyLotRequest;
use Auth;

class TradeController extends Controller
{
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function store(Request $request)
    {
        $lotRequest =  app()->make(BuyLotRequest::class, [
            'userId' => Auth::user()->id,
            'lotId' => $request->lot_id,
            'amount' => $request->amount
        ]);
        $trade = $this->marketService->buyLot($lotRequest);
        $response = ["message","trade #{$trade->id} added!"];
        return response()->json($response,201);
    }
}