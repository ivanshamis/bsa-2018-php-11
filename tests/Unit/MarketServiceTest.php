<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;

class MarketServiceTest extends TestCase
{
    private $marketService;

    protected function setUp()
    {
        parent::setUp();
        $this->marketService = $this->app->make(MarketService::class);
    }

    public function test_add_lot()
    {
        $this->assertTrue(true);
    }
}
