<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\Contracts\{MarketService,WalletService,CurrencyService};
use App\Request\Contracts\AddLotRequest;

class MarketServiceTest extends TestCase
{
    private $marketService;
    private $walletService;
    private $currencyService;

    protected function setUp()
    {
        parent::setUp();
        $this->marketService = $this->app->make(MarketService::class);
        $this->walletService = $this->app->make(WalletService::class);
        $this->currencyService = $this->app->make(CurrencyService::class);
    }

    public function test_add_lot()
    {
        $this->assertTrue(true);
    }
}
