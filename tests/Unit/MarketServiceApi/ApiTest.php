<?php

namespace Tests\Unit\MarketService;

use Tests\TestCase;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;
use App\Repository\Contracts\LotRepository;
use App\Entity\{Lot,Currency,Wallet,Money};
use Tests\TestDataFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Auth\AuthenticationException;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    const ENDPOINT = '/api/v1/lots';
    const ENDPOINT_TRADE = '/api/v1/trades';

    private $user;
    private $lotAdded;
    private $lotAddedMoneyAmount;
    private $lotToAdd;
    private $currency;
    private $addLotRequest;
    private $buyLotRequest;

    public function setUp() {
        parent::setUp();

        $this->currency = factory(Currency::class)->create();

        $user = factory(User::class)->create();
        $wallet = factory(Wallet::class)->make();
        $wallet->user_id = $user->id;
        $wallet->save();
        $money = factory(Money::class)->make();
        $money->currency_id = $this->currency->id;
        $money->wallet_id = $wallet->id;
        $lotAddedMoneyAmount = $money->amount;
        $money->save();
        $this->lotAdded = factory(Lot::class)->make();
        $this->lotAdded->currency_id = $this->currency->id;
        $this->lotAdded->seller_id = $user->id;
        $this->lotAdded->save();

        $this->user = factory(User::class)->create();
        $wallet = factory(Wallet::class)->make();
        $wallet->user_id = $this->user->id;
        $wallet->save();
        $money = factory(Money::class)->make();
        $money->currency_id = $this->currency->id;
        $money->wallet_id = $wallet->id;
        $money->save();
        $this->lotToAdd = factory(Lot::class)->make();
        $this->lotToAdd->currency_id = $this->currency->id;
        $this->lotToAdd->seller_id = $this->user->id;
        $this->addLotRequest = [
            'currency_id' => $this->lotToAdd->currency_id,
            'date_time_open' => $this->lotToAdd->GetDateTimeOpen(), 
            'date_time_close' => $this->lotToAdd->GetDateTimeClose(), 
            'price' => $this->lotToAdd->price
        ];

        $this->buyLotRequest = [
            'lot_id' => $this->lotAdded->id,
            'amount' => $lotAddedMoneyAmount
        ];
    }

    public function test_unauthorized_no_access() {
        $response = $this->json('GET', self::ENDPOINT);
        $response->assertStatus(403);
        $response->assertHeader('Content-Type', 'application/json');

        $response = $this->json('GET', self::ENDPOINT."/{$this->lotAdded->id}");
        $response->assertStatus(403);
        $response->assertHeader('Content-Type', 'application/json');

        $response = $this->json('POST', self::ENDPOINT, $this->addLotRequest);
        $response->assertStatus(403);
        $response->assertHeader('Content-Type', 'application/json');

        $response = $this->json('GET', self::ENDPOINT."/1000000");
        $response->assertStatus(403);
        $response->assertHeader('Content-Type', 'application/json');

        $response =$this->actingAs($this->user,'api')->json('GET', self::ENDPOINT."/1000000");
        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_authorized_get() {
        $response = $this->actingAs($this->user,'api')->json('GET', self::ENDPOINT);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $response = $this->actingAs($this->user,'api')
            ->json('GET', self::ENDPOINT."/{$this->lotAdded->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_authorized_post_lot() {
        $response = $this->actingAs($this->user,'api')
            ->json('POST', self::ENDPOINT, $this->addLotRequest);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');

        $this->assertDatabaseHas('lots', [
            'currency_id'=>$this->addLotRequest['currency_id'],
            'seller_id'=>$this->lotToAdd->seller_id
        ]);	
    }

    public function test_authorized_post_trade() {
        $response = $this->actingAs($this->user,'api')
            ->json('POST', self::ENDPOINT_TRADE, $this->buyLotRequest);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');

        $this->assertDatabaseHas('trades', [
            'lot_id'=>$this->buyLotRequest['lot_id']
        ]);	
    }

    public function test_authorized_post_lot_bad() {
        $request = $this->addLotRequest;
        $request['price'] = -1;
        $response = $this->actingAs($this->user,'api')
            ->json('POST', self::ENDPOINT, $request);
        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');	

        $request = $this->addLotRequest;
        $request['currency_id'] = 'currency';
        $response = $this->actingAs($this->user,'api')
            ->json('POST', self::ENDPOINT, $request);
        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');	
    }
}