<?php

namespace Tests\Unit\MarketService;

use Tests\TestCase;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;
use App\Repository\Contracts\LotRepository;
use App\Entity\Lot;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectPriceException,
    IncorrectTimeCloseException
};
use Tests\TestDataFactory;

class AddLotTest extends TestCase
{
    private $marketService;
    private $lotRepository;
    private $lots;
    private $lot;
    private $request;

    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(LotRepository::class);
        $this->lotRepository->method('add')
            ->will($this->returnCallback(function($lot) {
                return $this->add($lot);
        }));
        $this->lotRepository->method('findActiveCurrencyLot')
            ->will($this->returnCallback(function($userId,$currencyId) {
                return $this->findActiveCurrencyLot($userId,$currencyId);
        }));
        $this->lotRepository->method('getById')
            ->will($this->returnCallback(function($id) {
                return $this->getById($id);
        }));
        $this->lot = TestDataFactory::createLot();
        $this->lot = $this->lotRepository->add($this->lot);
        
        $this->marketService = $this->app->make(MarketService::class, [
            'lotRepository' => $this->lotRepository
        ]);

        $this->request = [
            'currencyId' => 1,
            'sellerId' => 1,
            'dateTimeOpen' => time(),
            'dateTimeClose' => time()+3600,
            'price' => 1
        ];
    }

    public function add(Lot $lot) : Lot
    {   
        if ($lot->id === NULL) {
            if (!is_array($this->lots)) {
                $lot->id = 1;
            }
            else {
                $lot->id = max(array_keys($this->lots))+1;
            }
        }
        $this->lots[$lot->id] = $lot; 
        return $lot;
    }

    public function findActiveCurrencyLot(int $userId, int $currencyId) : ?Lot
    {
        $activeLot = NULL;
        foreach ($this->lots as $lot) {
            if (
                ($lot->seller_id==$userId) && 
                ($lot->date_time_close>date('Y-m-d h:i:s', time())) &&
                ($lot->currency_id==$currencyId)
            ) {
                $activeLot = $lot;
                break;
            }
        }
        return $activeLot;
    }

    public function getById(int $id) : ?Lot
    {
        return $this->lots[$id];
    }

    public function test_add_lot_active_found()
    {   
        $this->request['currencyId'] = $this->lot->currency_id;
        $this->request['sellerId'] = $this->lot->seller_id;
        $lotRequest = $this->app->make(AddLotRequest::class, $this->request);

        $this->expectException(ActiveLotExistsException::class);

        $this->marketService->addLot($lotRequest);
    }

    public function test_add_lot_bad_time()
    {
        $this->request['dateTimeOpen'] = $this->request['dateTimeClose']+1000;
        $lotRequest = $this->app->make(AddLotRequest::class, $this->request);

        $this->expectException(IncorrectTimeCloseException::class);

        $this->marketService->addLot($lotRequest);
    }

    public function test_add_lot_bad_price()
    {
        $this->request['price'] = -1;
        $lotRequest = $this->app->make(AddLotRequest::class, $this->request);

        $this->expectException(IncorrectPriceException::class);

        $this->marketService->addLot($lotRequest);
    }

    public function test_add_lot_good()
    {
        $lotRequest = $this->app->make(AddLotRequest::class, $this->request);

        $lot  = $this->marketService->addLot($lotRequest);
        $this->assertInstanceOf(Lot::class, $lot);
        $this->assertEquals($lot,$this->lotRepository->getById($lot->id));
    }
}