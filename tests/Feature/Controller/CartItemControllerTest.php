<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;
use Laravel\Passport\Passport;

class CartItemControllerTest extends TestCase
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
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStore()
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->fakeUser->id
        ]);
        $product = Product::factory()->create(); // create 有新增進資料庫/ make 有建立未新增進資料庫
        $response = $this->call(
            'POST',
            'cart-items',
            ['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]
        );
        $response->assertOk();

        $product = Product::factory()->less()->create(); // 建立一個少的商品數量
        $response = $this->call(
            'POST',
            'cart-items',
            ['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 10]
        );
        $this->assertEquals($product->title.'數量不足', $response->getContent());

        $response = $this->call(
            'POST',
            'cart-items',
            ['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 9999]
        );
        $response->assertStatus(400);  // 回傳400 = True
    }

    public function testUpdate()
    {
        $cartItem = CartItem::factory()->create();
        $response = $this->call(
            'PUT',
            'cart-items/'.$cartItem->id,
            ['quantity' => 1]
        );
        $this->assertEquals('true', $response->getContent()); // assertEquals()查看前豆值是否相同

        $cartItem->refresh(); // 重新更新資料

        $this->assertEquals(1, $cartItem->quantity);
    }

    public function testDestroy()
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->fakeUser->id
        ]);
        $product = Product::factory()->create();
        $cartItem = $cart->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);
        $response = $this->call(
            'DELETE',
            'cart-items/'.$cartItem->id,
            ['quantity' => 1]
        );
        $response->assertOk();

        $cartItem = CartItem::find($cartItem->id);
        $this->assertNull($cartItem);
    }
}
