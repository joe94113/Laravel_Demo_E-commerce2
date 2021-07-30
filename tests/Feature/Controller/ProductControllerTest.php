<?php

namespace Tests\Feature\Controller;

use App\Http\Services\ShortUrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;
use Laravel\Passport\Passport;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $fakeUser;

    protected function setup(): void  // 覆寫
    {
        parent::setUp();
        $this->fakeUser = User::create(['name' => 'joe',
                                        'email' => 'joe94113@gmail.com',
                                        'password' => 123456789]);
        Passport::actingAs($this->fakeUser);
    }

    public function testSheredUrl()
    {
        $product = Product::factory()->create();
        $id = $product->id;
        $this->mock(ShortUrlService::class, function($mock) use($id){  // 略過執行，不送出
            $mock->shouldReceive('makeShortUrl')
                 ->with("http://localhost:3000/products/$id")
                 ->andReturn('fakeUrl');
        });

        $reponse = $this->call(
            'GET',
            'products/'.$id.'/shared-url'
        );
        $reponse->assertOk();
        $reponse = json_decode($reponse->getContent(), true);
        $this->assertEquals($reponse['url'], 'fakeUrl');
    }
}
