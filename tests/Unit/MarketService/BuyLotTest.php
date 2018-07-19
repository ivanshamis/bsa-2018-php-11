<?php

namespace Tests\Unit\MarketService;

use Tests\TestCase;
use App\Service\Contracts\{MarketService,WalletService};
use App\Request\Contracts\BuyLotRequest;
use App\Repository\Contracts\{LotRepository,WalletRepository,MoneyRepository,TradeRepository,UserRepository};
use App\Entity\{Lot,Trade,Wallet,Money};
use App\Exceptions\MarketException\{
    BuyOwnCurrencyException,
    IncorrectLotAmountException,
    BuyNegativeAmountException,
    BuyInactiveLotException,
    LotDoesNotExistException    
};
use Tests\TestDataFactory;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCreated;

class BuyLotTest extends TestCase
{
    private $marketService;
    private $walletService;
    private $lotRepository;
    private $walletRepository;
    private $moneyRepository;
    private $tradeRepository;
    private $userRepository;
    private $lots;
    private $wallets;
    private $users;
    private $monies;
    private $lot;
    private $userWallet;
    private $sellerWallet;
    private $userMoney;
    private $sellerMoney;
    private $request;
    private $user;
    private $seller;
    private $tradeAdded;

    protected function setUp()
    {
        parent::setUp();

        $this->setUpUserRepositoryMock();
        $this->setUpLotRepositoryMock();
        $this->setUpWalletRepositoryMock();
        $this->setUpMoneyRepositoryMock();
        $this->setUpTradeRepositoryMock();

        $this->walletService = $this->app->make(WalletService::class, [
            'walletRepository' => $this->walletRepository,
            'moneyRepository' => $this->moneyRepository
        ]);
        
        $this->marketService = $this->app->make(MarketService::class, [
            'lotRepository' => $this->lotRepository,
            'walletRepository' => $this->walletRepository,
            'moneyRepository' => $this->moneyRepository,
            'tradeRepository' => $this->tradeRepository,
            'userRepository' => $this->userRepository,
            'walletService' => $this->walletService
        ]);

        $this->request = [
            'userId' => $this->user->id,
            'lotId' => $this->lot->id,
            'amount' => 100
        ];
    }

    protected function setUpLotRepositoryMock() {
        $this->lotRepository = $this->createMock(LotRepository::class);
        $this->lotRepository->method('add')
            ->will($this->returnCallback(function($lot) {
                return $this->addLot($lot);
        }));
        $this->lotRepository->method('getById')
            ->will($this->returnCallback(function($id) {
                return $this->getLotById($id);
        }));
        $this->lot = TestDataFactory::createLot();
        $this->lot->seller_id = $this->seller->id;
        $this->lot = $this->lotRepository->add($this->lot);
    }

    protected function setUpWalletRepositoryMock() {
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->walletRepository->method('add')
            ->will($this->returnCallback(function($wallet) {
                return $this->addWallet($wallet);
        }));
        $this->walletRepository->method('findByUser')
            ->will($this->returnCallback(function($id) {
                return $this->findWalletByUser($id);
        }));
        $this->userWallet = TestDataFactory::createWallet();
        $this->userWallet->user_id = $this->user->id;
        $this->userWallet = $this->walletRepository->add($this->userWallet);
        $this->sellerWallet = TestDataFactory::createWallet();
        $this->sellerWallet->user_id = $this->lot->seller_id;
        $this->sellerWallet = $this->walletRepository->add($this->sellerWallet);
    }

    protected function setUpMoneyRepositoryMock() {
        $this->moneyRepository = $this->createMock(MoneyRepository::class);
        $this->moneyRepository->method('findByWalletAndCurrency')
            ->will($this->returnCallback(function($walletId, $currencyId) {
                return $this->findMoneyByWalletAndCurrency($walletId, $currencyId);
        }));
        $this->moneyRepository->method('save')
            ->will($this->returnCallback(function($money) {
                return $this->saveMoney($money);
        }));
        $this->userMoney = TestDataFactory::createMoney();
        $this->userMoney->currency_id = $this->lot->currency_id;
        $this->userMoney->wallet_id = $this->userWallet->id;
        $this->moneyRepository->save($this->userMoney);
        $this->sellerMoney = TestDataFactory::createMoney();
        $this->sellerMoney->currency_id = $this->lot->currency_id;
        $this->sellerMoney->wallet_id = $this->sellerWallet->id;
        $this->moneyRepository->save($this->sellerMoney);
    }

    protected function setUpTradeRepositoryMock() {
        $this->tradeRepository = $this->createMock(TradeRepository::class);
        $this->tradeRepository->method('add')
            ->will($this->returnCallback(function($trade) {
                return $this->addTrade($trade);
        }));
    }

    protected function setUpUserRepositoryMock() {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository->method('getById')
            ->will($this->returnCallback(function($id) {
                return $this->getUserById($id);
        }));
        $this->userRepository->method('add')
            ->will($this->returnCallback(function($user) {
                return $this->addUser($user);
        }));
        $this->seller = TestDataFactory::createUser();
        $this->user = TestDataFactory::createUser();

        $this->user = $this->userRepository->add($this->user);
        $this->seller = $this->userRepository->add($this->seller);
    }
    
    protected function addLot(Lot $lot) : Lot
    {   
        if (!is_array($this->lots)) {
            $lot->id = 1;
        }
        else {
            $lot->id = max(array_keys($this->lots))+1;
        }
        $this->lots[$lot->id] = $lot; 
        return $lot;
    }

    protected function AddTrade(Trade $trade): Trade 
    {
        $this->tradeAdded = true;
        return $trade;
    }

    protected function saveMoney(Money $money): Money
    {   
        if (!is_array($this->monies)) {
            $money->id = 1;
        }
        else {
            if ($money->id === NULL) {
                $money->id = max(array_keys($this->monies))+1;
            }
        }
        $this->monies[$money->id] = $money; 
        return $money;
    }

    protected function addUser(User $user) : User
    {   
        if (!is_array($this->users)) {
            $user->id = 1;
        }
        else {
            $user->id = max(array_keys($this->users))+1;
        }
        $this->users[$user->id] = $user; 
        return $user;
    }

    protected function addWallet(Wallet $wallet) : Wallet
    {   
        if (!is_array($this->wallets)) {
            $wallet->id = 1;
        }
        else {
            $wallet->id = max(array_keys($this->wallets))+1;
        }
        $this->wallets[$wallet->id] = $wallet; 
        return $wallet;
    }

    protected function getLotById(int $id) : ?Lot
    {
        return $this->lots[$id];
    }

    protected function getUserById(int $id) : ?User
    {
        return $this->users[$id];
    }

    protected function findWalletByUser(int $userId) : ?Wallet
    {
        $findWallet = NULL;
        foreach ($this->wallets as $wallet) {
            if ($wallet->user_id==$userId) {
                $findWallet = $wallet;
                break;
            }
        }        
        return $findWallet;
    }

    protected function findMoneyByWalletAndCurrency(int $walletId, int $currencyId) : ?Money
    {
        $findMoney = NULL;
        foreach ($this->monies as $money) {
            if (($money->wallet_id==$walletId) and ($money->currency_id==$currencyId)) {
                $findMoney = $money;
                break;
            }
        }        
        return $findMoney;
    }
    
    public function test_buy_lot_good()
    {
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);

        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency(
            $this->walletRepository->findByUser($this->lot->seller_id)->id,
            $this->lot->currency_id
        );
        $sellerAmountBefore = $sellerMoney->amount;
        
        $userMoney = $this->moneyRepository->findByWalletAndCurrency(
            $this->walletRepository->findByUser($lotRequest->getUserId())->id,
            $this->lot->currency_id
        );
        $userAmountBefore = $userMoney->amount;

        $this->tradeAdded = false;
        Mail::fake();
        $trade  = $this->marketService->buyLot($lotRequest);

        $sellerAmountAfter = $sellerMoney->amount;
        $userAmountAfter = $userMoney->amount;
        
        $this->assertEquals($lotRequest->getAmount(), $sellerAmountBefore-$sellerAmountAfter);
        $this->assertEquals($lotRequest->getAmount(), $userAmountAfter-$userAmountBefore);

        $this->assertInstanceOf(Trade::class, $trade);
        $this->assertTrue($this->tradeAdded);

        Mail::assertSent(TradeCreated::class, function ($mail) use ($trade) {
            return $mail->trade->id === $trade->id;
        });
    }

    public function test_buy_lot_own()
    {
        $this->request['userId'] = $this->lot->seller_id;
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);
        $this->expectException(BuyOwnCurrencyException::class);
        $this->marketService->buyLot($lotRequest);
    }

    public function test_buy_lot_negative()
    {
        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency(
            $this->walletRepository->findByUser($this->lot->seller_id)->id,
            $this->lot->currency_id
        );
        $this->request['amount'] = $sellerMoney->amount+1;
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);
        $this->expectException(BuyNegativeAmountException::class);
        $this->marketService->buyLot($lotRequest);
    }

    public function test_buy_lot_bad_price()
    {
        $this->request['amount'] = 0.5;
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);
        $this->expectException(IncorrectLotAmountException::class);
        $this->marketService->buyLot($lotRequest);

        $this->request['amount'] = -1;
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);
        $this->expectException(IncorrectLotAmountException::class);
        $this->marketService->buyLot($lotRequest);
    }

    public function test_buy_lot_inactive()
    {
        $this->lot->date_time_close = time()-1000;
        $lotRequest = $this->app->make(BuyLotRequest::class, $this->request);
        $this->expectException(BuyInactiveLotException::class);
        $this->marketService->buyLot($lotRequest);
    }
}