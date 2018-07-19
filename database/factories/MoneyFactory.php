<?php

use Faker\Generator as Faker;
use App\Entity\{Currency,Wallet,Money};

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(Money::class, function (Faker $faker) {
    return [
        'currency_id' => Currency::inRandomOrder()->first()->id,
        'amount' => $faker->randomFloat(2,0,1000000),
        'wallet_id' => Wallet::inRandomOrder()->first()->id,
    ];
});