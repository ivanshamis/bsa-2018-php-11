<?php

use Faker\Generator as Faker;
use App\Entity\{Lot,Currency};
use App\User;
use Carbon\Carbon;

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
$factory->define(Lot::class, function (Faker $faker) {
    return [
        'seller_id' => $faker->numberBetween(1,99),
        'currency_id' => $faker->numberBetween(1,99),
        //'seller_id' => User::inRandomOrder()->first()->id,
        //'currency_id' => Currency::inRandomOrder()->first()->id,
        'date_time_open' => Carbon::createFromTimestamp((int) time()),
        'date_time_close' => Carbon::createFromTimestamp((int) time()+3600),
        'price' => $faker->randomFloat(2,0,1000),
    ];
});