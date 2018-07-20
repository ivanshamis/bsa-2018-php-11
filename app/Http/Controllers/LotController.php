<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\Contracts\MarketService;
use App\Request\Contracts\AddLotRequest;
use App\Repository\Contracts\{UserRepository,CurrencyRepository};
use App\User;
use App\Entity\Currency;
use App\Http\Requests\LotRequest;

class LotController extends Controller
{
    private $marketService;

    public function __construct(
        MarketService $marketService,
        UserRepository $userRepository,
        CurrencyRepository $currencyRepository
        )
    {
        $this->marketService = $marketService;
        $this->userRepository = $userRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function store(LotRequest $request)
    {
        $user = $this->userRepository->getFakeUser();
         
        $currency = $this->currencyRepository->getCurrencyByName($request->currency_name);
        if ($currency===NULL) {
            $message = 'No such currency!';
            return view('lots-store')->with('message', $message);    
        }

        $dateTimeOpen = time();
        $dateTimeClose = $dateTimeOpen + strtotime($request->time_close);

        $lotRequest =  app()->make(AddLotRequest::class, [
            'currencyId' => $currency->id,
            'sellerId' => $user->id,
            'dateTimeOpen' => $dateTimeOpen,
            'dateTimeClose' => $dateTimeClose,
            'price' => $request->price
        ]);

        $message = 'Lot has been added successfully!';
        return view('lots-store')->with('message', $message);
    }


    public function create()
    {
        return view('lots-add');
    }
}
