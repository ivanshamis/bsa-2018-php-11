<?php

use Faker\Generator as Faker;
use App\Entity\Lot;

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
        'date_time_open' => time(),
        'date_time_close' => time() + $faker->numberBetween(1200,3600),
        'price' => $faker->randomFloat(2,0,1000),
    ];
});