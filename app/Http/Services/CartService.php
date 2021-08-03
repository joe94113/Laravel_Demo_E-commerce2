<?php

namespace App\Http\Services;
use Illuminate\Support\Facades\DB;

class CartService
{
    const VIP_LEVEL = 2;
    const VIP_RATE = 0.8;
    const NORMAL_RATE = 1;

    public function checkout($cart)
    {
        DB::beginTransaction();

        try {
            $lackCartItem = $this->checkLackCartItem($cart->cartItems);
            if($lackCartItem){ // 產品數量不足
                return $lackCartItem->product->title.'數量不足';
            }

            $rate = $this->cartRate($cart);
            $order = $this->createOrder($cart, $rate);
            $cart->update(['checkouted' => true]);
            $order->orderItems;
            DB::commit();
            return $order;
        } catch (\Throwable $th) {
            DB::rollBack();
            return "something error";
        }
    }

    public function checkLackCartItem($cartItems) // 確認商品數量
    {
        return $cartItems->fillter(function ($cartItem) {
            return $cartItem->product->quantity < $cartItem->quantity; // 如果購物車數量大於產品數量
        })->first();
    }

    public function cartRate($cart)  // 判斷用戶等級
    {
        // 如果用戶等級為2，費率變成0.8
        return $cart->user->level == self::VIP_LEVEL ? self::VIP_RATE : self::NORMAL_RATE;
    }
    
    public function createOrder($cart, $rate)
    {
        $order = $cart->order()->create([
            'user_id' => $cart->user_id
        ]);

        foreach($this->cartItems as $cartItem){
            $order->orderItems()->create([
                'product_id' => $cartItem->product_id,
                'price' => $cartItem->product->price * $rate
            ]);
            $cartItem->product->update(['quantity' => $cartItem->product->quantity - $cartItem->quantity]);
        }
        return $order;
    }
}