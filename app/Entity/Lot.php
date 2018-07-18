<?php

namespace App\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Service\WalletService;
use App\User;

class Lot extends Model
{
    protected $fillable = [
        "id",
        'currency_id',
        'seller_id',
        'date_time_open',
        'date_time_close',
        'price'
    ];

    public function getSeller(): User 
    {
        return User::find($this->seller_id);
    }

    public function getCurrency(): Currency
    {
        return Currency::find($this->currency_id);
    }

    public function getAmount(Walletservice $walletService)
    {
        $walletId = $walletService->WalletIdByUserId($this->seller_id);
        return $walletService->getMoney($walletId, $this->currency_id)->amount;        
    }

    public function getDateTimeOpen() : int
    {
        if (is_int($this->date_time_open)) {
            return $this->date_time_open;
        } else {
            return (new Carbon($this->date_time_open))->getTimestamp();
        }
    }

    public function getDateTimeClose() : int
    {
        if (is_int($this->date_time_close)) {
            return $this->date_time_close;
        } else {
            return (new Carbon($this->date_time_close))->getTimestamp();
        }
    }
}
