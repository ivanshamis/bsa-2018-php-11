<?php

use Illuminate\Database\Seeder;
use App\Entity\Currency;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency = factory(Currency::class)->make();
        $currency->name = 'CUR1';
        $currency->save();
        factory(App\Entity\Currency::class, 2)->create();
    }
}