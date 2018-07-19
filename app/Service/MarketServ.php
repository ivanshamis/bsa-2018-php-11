<?php

namespace App\Service;

use App\Entity\{Lot, Trade, Wallet, Money};
use App\Request\Contracts\{AddLotRequest,BuyLotRequest,MoneyRequest};
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
use App\Service\Contracts\{MarketService,WalletService};
use App\Mail\TradeCreated;
use App\Repository\Contracts\{
        LotRepository,
        WalletRepository,
        MoneyRepository,
        TradeRepository,
        UserRepository,
        CurrencyRepository
};
use Illuminate\Support\Facades\Mail;
use App\User;


class MarketServ implements MarketService
{
    private $lotRepository; 
    private $walletRepository; 
    private $moneyRepository;
    private $tradeRepository;
    private $userRepository; 
    private $currencyRepository; 
    private $walletService;
    private $moneyRequest;

    public function __construct(
        LotRepository $lotRepository, 
        WalletRepository $walletRepository,
        MoneyRepository $moneyRepository,
        TradeRepository $tradeRepository,
        UserRepository $userRepository,
        CurrencyRepository $currencyRepository,
        WalletService $walletService,
        MoneyRequest $moneyRequest)
    {
        $this->lotRepository = $lotRepository;
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
        $this->tradeRepository = $tradeRepository;
        $this->userRepository = $userRepository;
        $this->currencyRepository = $currencyRepository;
        $this->walletService = $walletService;
        $this->moneyRequest = $moneyRequest;
    }
    
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
        $lot = $this->lotRepository->findActiveCurrencyLot(
            $lotRequest->getSellerId(), 
            $lotRequest->getCurrencyId()
        );
        if ($lot!==NULL) {
            throw new ActiveLotExistsException;
        }
        if ($lotRequest->getDateTimeClose()<$lotRequest->getDateTimeOpen()) {
            throw new IncorrectTimeCloseException;
        }
        if ($lotRequest->getPrice()<0) {
            throw new IncorrectPriceException;
        }

        $lot = new Lot;
        $lot->fill([
            'currency_id' => $lotRequest->getCurrencyId(),
            'seller_id' => $lotRequest->getSellerId(),
            'date_time_open' => $lotRequest->getDateTimeOpen(),
            'date_time_close' => $lotRequest->getDateTimeClose(),
            'price' => $lotRequest->getPrice()
        ]);
        return $this->lotRepository->add($lot);
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
        $lot = $this->lotRepository->getById($lotRequest->getLotId());
        if ($lot === NULL) {
            throw new LotDoesNotExistException;   
        }
        if ($lot->getDateTimeClose() < time()) {
            throw new BuyInactiveLotException;
        }
        if ($lot->seller_id === $lotRequest->getUserId()) {
            throw new BuyOwnCurrencyException;
        }
        if ($lotRequest->getAmount() < 1) {
            throw new IncorrectLotAmountException;
        }
        $sellerWallet = $this->walletRepository->findByUser($lot->seller_id); 
        $lotMoney = $this->moneyRepository->findByWalletAndCurrency($sellerWallet->id, $lot->currency_id);
        if ($lotRequest->getAmount() > $lotMoney->amount) {
            throw new BuyNegativeAmountException();
        }
        $userWallet = $this->walletRepository->findByUser($lotRequest->getUserId());

        $this->moneyRequest->setWalletId($sellerWallet->id);
        $this->moneyRequest->setCurrencyId($lot->currency_id);
        $this->moneyRequest->setAmount($lotRequest->getAmount());
        $this->walletService->takeMoney($this->moneyRequest);

        $this->moneyRequest->setWalletId($userWallet->id);
        $this->walletService->addMoney($this->moneyRequest);

        $trade = new Trade;
        $trade->fill([
            'lot_id' => $lotRequest->getLotId(),
            'user_id' => $lotRequest->getUserId(),
            'amount' => $lotRequest->getAmount()
        ]);
        $trade = $this->tradeRepository->add($trade);

        $tradeCreated = new TradeCreated($trade);
        $user = $this->userRepository->getById($lot->seller_id);
        Mail::to($user)->send($tradeCreated);

        return $trade;
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
        $lot = $this->lotRepository->getById($id);
        if ($lot===NULL) {
            throw new LotDoesNotExistException;
        }
        return app()->make(LotResponse::class, [
            'lot' => $lot,
            'userRepository' => $this->userRepository,
            'currencyRepository' => $this->currencyRepository,
            'moneyRepository' => $this->moneyRepository,
            'walletRepository' => $this->walletRepository
        ]);
    }

    /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList() : array
    {
        $lotResponse = [];
        $lots = $this->lotRepository->findAll();
        if (is_array($lots)) {
            foreach ($lots as $lot) {
                $lotResponse[] = $this->getLot($lot->id);  
            }
        }        
        return $lotResponse;
    }
}