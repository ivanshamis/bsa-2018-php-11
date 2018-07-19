<?php

namespace Tests\Unit\MarketService;

use Tests\TestCase;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;
use App\Repository\Contracts\{LotRepository,UserRepository,CurrencyRepository,MoneyRepository,WalletRepository};
use App\Entity\{Lot,Currency,Money,Wallet};
use App\Exceptions\MarketException\LotDoesNotExistException;
use Tests\TestDataFactory;
use App\User;
use App\Response\Contracts\LotResponse;

class GetLotTest extends TestCase
{
    private $marketService;
    private $lotRepository;
    private $userRepository;
    private $moneyRepository;
    private $lot;
    private $lots;
    private $user;
    private $users;
    private $seller;
    private $currency;
    private $currencies;
    private $userMoney;
    private $sellerMoney;
    private $monies;
    private $wallets;

    protected function setUp()
    {
        parent::setUp();

        $this->setUpUserRepositoryMock();
        $this->setUpLotRepositoryMock();
        $this->setUpCurrencyRepositoryMock();
        $this->setUpMoneyRepositoryMock();
        $this->setUpWalletRepositoryMock();

        $this->marketService = $this->app->make(MarketService::class, [
            'lotRepository' => $this->lotRepository,
            'userRepository' => $this->userRepository,
            'currencyRepository' => $this->currencyRepository,
            'moneyRepository' => $this->moneyRepository,
            'walletRepository' => $this->walletRepository
        ]);
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

    protected function setUpCurrencyRepositoryMock() {
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->currencyRepository->method('add')
            ->will($this->returnCallback(function($currency) {
                return $this->addCurrency($currency);
        }));
        $this->currencyRepository->method('getById')
            ->will($this->returnCallback(function($id) {
                return $this->getCurrencyById($id);
        }));
        $this->currency = TestDataFactory::createCurrency();
        $this->currency->id = $this->lot->currency_id;
        $this->currency = $this->currencyRepository->add($this->currency);
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
        $this->userMoney->wallet_id = 1;
        $this->moneyRepository->save($this->userMoney);
        $this->sellerMoney = TestDataFactory::createMoney();
        $this->sellerMoney->currency_id = $this->lot->currency_id;
        $this->sellerMoney->wallet_id = 2;
        $this->moneyRepository->save($this->sellerMoney);
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

    protected function getUserById(int $id) : ?User
    {
        return $this->users[$id];
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

    protected function addCurrency(Currency $currency) : Currency
    {   
        if ($currency->id === NULL) {
            if (!is_array($this->currencies)) {
                $currency->id = 1;
            }
            else { 
                $currency->id = max(array_keys($this->currencies))+1;
            }
        }
        $this->currencies[$currency->id] = $currency; 
        return $currency;
    }

    protected function getLotById(int $id) : ?Lot
    {
        return $this->lots[$id];
    }

    protected function getCurrencyById(int $id) : ?Currency
    {
        return $this->currencies[$id];
    }

    public function test_get_lot_good()
    {
        $lotResponse = $this->marketService->getLot($this->lot->id);

        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency(
            $this->walletRepository->findByUser($this->lot->seller_id)->id,
            $this->lot->currency_id
        );
        
        $this->assertEquals($lotResponse->getAmount(), $sellerMoney->amount);

        $dateTimeOpen = date('Y/m/d h:i:s', $this->lot->getDatetimeOpen());
        $this->assertEquals($lotResponse->getDateTimeOpen(), $dateTimeOpen);

        $dateTimeClose = date('Y/m/d h:i:s', $this->lot->getDatetimeClose());
        $this->assertEquals($lotResponse->getDateTimeClose(), $dateTimeClose);

        $price = number_format($this->lot->price,2,",","");
        $this->assertEquals($lotResponse->getPrice(), $price);
    }

    /*public function test_get_lots_good()
    {
        $lotResponse = $this->marketService->getLotList($this->lot->id);
    }*/
}