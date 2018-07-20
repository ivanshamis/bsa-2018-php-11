<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Entity\Currency;

class AddCurrencyPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_form_is_present()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add')
                    ->assertPresent('form');
            }
        );
    }

    public function test_all_fields_are_empty()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add');
                $this->assertEmpty($browser->value('input[name=currency_name]'));
                $this->assertEmpty($browser->value('input[name=price]'));
                $this->assertEmpty($browser->value('input[name=time_close]'));
            }
        );
    }

    public function test_validates()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add');
                $browser->press('Save')
                    ->assertPathIs('/market/lots/add')
                    ->assertSee('The currency name field is required.')
                    ->assertSee('The time close field is required.')
                    ->assertSee('The price field is required.');
                $browser
                    ->value('input[name=currency_name]', 'Coin')
                    ->value('input[name=price]', 'hello')
                    ->value('input[name=time_close]', 'hi')
                    ->press('Save')
                    ->assertSee('The time close field is required.')
                    ->assertSee('The price must be a number.');
                $browser
                    ->value('input[name=currency_name]', '')
                    ->value('input[name=price]', -2)
                    ->press('Save')
                    ->assertSee('The currency name field is required.')
                    ->assertSee('The price must be at least 0.');
            }
        );
    }

    public function test_old_values_on_error()
    {
        $this->browse(
            function (Browser $browser) {
                $value = 'test';
                $browser->visit('/market/lots/add')
                    ->value('input[name=currency_name]', $value)
                    ->value('input[name=price]', $value)
                    ->press('Save');
                $this->assertEquals($browser->value('input[name=currency_name]'), $value);
                $this->assertEquals($browser->value('input[name=price]'), $value);
            }
        );
    }

    public function test_unknown_currency()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add')
                    ->value('input[name=currency_name]', 'Unknown Currency')
                    ->value('input[name=price]', '9999')
                    ->value('input[name=time_close]', '01:00')
                    ->press('Save')
                    ->assertPathIs('/market/lots')
                    ->assertSee('Sorry, error has been occurred')
                    ->assertSee('No such currency');
            }
        );
    }

    public function test_save_currency()
    {
        $currency = factory(Currency::class)->make();
        $currency->name = 'Good Currency';
        $currency->save();

        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add')
                    ->value('input[name=currency_name]', 'Good Currency')
                    ->value('input[name=price]', '9999')
                    ->value('input[name=time_close]', '01:00')
                    ->press('Save')
                    ->assertPathIs('/market/lots')
                    ->assertSee('Lot has been added successfully!');
            }
        );
    }

}