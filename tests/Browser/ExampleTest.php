<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations; // 執行測試前，會先 rollback ，再重新 migrate
    /**
     * A basic browser test example.
     *
     * @return void
     */
    protected function setup():void
    {
        parent::setUp();
        User::factory()->create([
            'email' => 'joe94113@gmail.com'
        ]);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            // '--disable-gpu',
            // '--headless'
        ]);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('商品列表')
                    ->assertSee('聯絡我們');

            $browser->click('.check_product')
                    ->waitForDialog(5) // 等待五秒
                    ->assertDialogOpened('商品數量充足')
                    ->acceptDialog();  // 點擊確定
            // eval(\Psy\sh());
        });
    }

    public function testFillForm()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact-us')
                    ->value('[name="name"]', 'cool')
                    ->select('[name="product"]', '食物')
                    ->pause(1000)
                    ->press('送出')
                    ->assertQueryStringHas('product', '食物');
            // eval(\Psy\sh());
        });
    }
}
