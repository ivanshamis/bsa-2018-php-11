<?php

use Faker\Generator as Faker;

use App\Entity\Currency;

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
$factory->define(Currency::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company,
    ];
});