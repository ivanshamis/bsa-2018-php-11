<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;
use Auth;

class LotController extends Controller
{
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function index()
    {
        $lotResponses = $this->marketService->getLotList();
        $response = [];
        foreach ($lotResponses as $lotResponse) {
            $response[] = $this->lotResponseToArray($lotResponse);
        }
        return response()->json($response,200);
    }

    public function store(Request $request)
    {
        $lotRequest =  app()->make(AddLotRequest::class, [
            'currencyId' => $request->currency_id,
            'sellerId' => Auth::user()->id,
            'dateTimeOpen' => $request->date_time_open,
            'dateTimeClose' => $request->date_time_close,
            'price' => $request->price
        ]);
        $lot = $this->marketService->addLot($lotRequest);
        $lotResponse = $this->marketService->getLot($lot->id);
        $response = $this->lotResponseToArray($lotResponse);
        return response()->json($response,201);
    }


    public function show(int $id)
    {
        $lotResponse = $this->marketService->getLot($id);
        $response = $this->lotResponseToArray($lotResponse);
        return response()->json($response,200);
    }

    private function lotResponseToArray($lotResponse)
    {
        return [
            'id' => $lotResponse->getId(),
            'user_name' => $lotResponse->getUserName(),
            'currency_name' => $lotResponse->getCurrencyName(),
            'amount' => $lotResponse->getAmount(),
            'date_time_open' => $lotResponse->getDateTimeOpen(),
            'date_time_close' => $lotResponse->getDateTimeClose(),
            'price' => $lotResponse->getPrice()
        ];
    }
}
