<?php

use Faker\Generator as Faker;
use App\Entity\{User,Wallet};

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
$factory->define(Wallet::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
    ];
});