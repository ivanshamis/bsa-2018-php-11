<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repository\Contracts\{CurrencyRepository,LotRepository,MoneyRepository};
use App\Repository\Contracts\{TradeRepository,WalletRepository,UserRepository};
use App\Repository\{CurrencyRepo,LotRepo,MoneyRepo,TradeRepo,WalletRepo,UserRepo};
use App\Service\Contracts\{CurrencyService,MarketService,WalletService};
use App\Service\{CurrencyServ,MarketServ,WalletServ};
use App\Request\Contracts\{AddCurrencyRequest,AddLotRequest,BuyLotRequest};
use App\Request\Contracts\{CreateWalletRequest,MoneyRequest};
use App\Request\{AddCurrencyReq,AddLotReq,BuyLotReq,CreateWalletReq,MoneyReq};
use App\Response\Contracts\LotResponse;
use App\Response\LotRespo;
use Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Route::resourceVerbs([
            'create' => 'add',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CurrencyRepository::class, CurrencyRepo::class);
        $this->app->bind(LotRepository::class, LotRepo::class);
        $this->app->bind(MoneyRepository::class, MoneyRepo::class);
        $this->app->bind(TradeRepository::class, TradeRepo::class);
        $this->app->bind(WalletRepository::class, WalletRepo::class);
        $this->app->bind(UserRepository::class, UserRepo::class);
        $this->app->bind(CurrencyService::class, CurrencyServ::class);
        $this->app->bind(MarketService::class, MarketServ::class);
        $this->app->bind(WalletService::class, WalletServ::class);
        $this->app->bind(AddCurrencyRequest::class, AddCurrencyReq::class);
        $this->app->bind(AddLotRequest::class, AddLotReq::class);
        $this->app->bind(BuyLotRequest::class, BuyLotReq::class);
        $this->app->bind(CreateWalletRequest::class, CreateWalletReq::class);
        $this->app->bind(MoneyRequest::class, MoneyReq::class);
        $this->app->bind(LotResponse::class, LotRespo::class);
    }
}