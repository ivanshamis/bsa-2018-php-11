<?php

namespace App\Service;

use App\Entity\{Lot, Trade, Wallet};
use App\Request\Contracts\{ AddLotRequest, BuyLotRequest };
use App\Response\Contracts\LotResponse;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectPriceException,
    IncorrectTimeCloseException,
    BuyOwnCurrencyException,
    IncorrectLotAmountException,
    BuyNegativeAmountException,
    BuyInactiveLotException,
    LotDoesNotExistException
};
use App\Service\Contracts\MarketService;
use App\Service\Contracts\WalletService;
use App\Mail\TradeCreated;


class MarketServ implements MarketService
{
    /**
     * Sell currency.
     *
     * @param AddLotRequest $lotRequest
     * 
     * @throws ActiveLotExistsException
     * @throws IncorrectTimeCloseException
     * @throws IncorrectPriceException
     *
     * @return Lot
     */
    public function addLot(AddLotRequest $lotRequest) : Lot
    {
        $lot = Lot::where('currency_id', $lotRequest->getCurrencyId())->first();
        if ($lot!==NULL) {
            throw new ActiveLotExistsException;
        }
        if ($lotRequest->getDateTimeClose<$lotRequest->getDateTimeOpen) {
            throw new IncorrectTimeCloseException;
        }
        if ($lotRequest->getPrice<0) {
            throw new IncorrectPriceException;
        }
        return Lot::create([
            'currency_id' => $lotRequest->getCurrencyId(),
            'seller_id' => $lotRequest->getSellerId(),
            'date_time_open' => $lotRequest->getDateTimeOpen(),
            'date_time_close' => $lotRequest->getDateTimeClose(),
            'price' => $lotRequest->getPrice()
        ]);
    }   

    /**
     * Buy currency.
     *
     * @param BuyLotRequest $lotRequest
     * 
     * @throws BuyOwnCurrencyException
     * @throws IncorrectLotAmountException
     * @throws BuyNegativeAmountException
     * @throws BuyInactiveLotException
     * 
     * @return Trade
     */
    public function buyLot(BuyLotRequest $lotRequest) : Trade
    {
        $lot = getLot($lotRequest->id);
        if ($lot===NULL) {
            throw new BuyInactiveLotException;
        }
        if ($lot->getDateTimeClose() > date('Y/m/d h:i:s')) {
            throw new BuyInactiveLotException;
        }
        if ($lot->seller_id === $lotRequest->getUserId()) {
            throw new BuyOwnCurrencyException;
        }
        if ($lotRequest->getAmount() < 1) {
            throw new IncorrectLotAmountException;
        }
        if ($lotRequest->getAmount() > $lot->getAmount()) {
            throw new BuyNegativeAmountException;
        }
         
        $walletService = App::make(WalletService::class);
        $moneyRequest = App::make(MoneyRequest::class, [
            'walletId' => $walletService->WalletIdByUserId($lot->seller_id),
            'currencyId'=> $lot->currency_id,
            'amount'=> $lotRequest->getAmount()
        ]);
        $walletSerrvice->takeMoney($moneyRequest);

        $moneyRequest = App::make(MoneyRequest::class, [
            'walletId' => $walletService->WalletIdByUserId($lotRequest->userId),
            'currencyId'=> $lot->currency_id,
            'amount'=> $lotRequest->getAmount()
        ]);
        $walletSerrvice->addMoney($moneyRequest);

        $trade =  Trade::create([
            'lot_id' => $lotRequest->getLotId(),
            'user_id' => $lotRequest->getUserId(),
            'amount' => $lotRequest->getAmount()
        ]);

        $tradeCreated = new TradeCreated($trade);
        Mail::to(User::find($lot->seller_id))->send($tradeCreated);
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     * 
     * @throws LotDoesNotExistException
     * 
     * @return LotResponse
     */
    public function getLot(int $id) : LotResponse
    {
        $lot = Lot::find($id);
        if ($lot===NULL) {
            throw new LotDoesNotExistException;
        }
        return new LotResponse($lot);
    }

    /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList() : array
    {
        $lotResponse = [];
        $lots = Lot::all();
        foreach ($lots as $lot) {
            $LotResponse[] = $this->getLot($lot);  
        }        
        return $lotResponse;
    }
}