<?php

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'WebController@index');  // 商品列表
Route::get('/contact-us', 'WebController@contactUs'); // 聯絡我們
Route::post('/read-notification', 'WebController@readNotifications'); // 讀取notifications

Route::group(['middleware' => 'check.dirty'], function(){
    Route::resource('/product', 'ProductController');
});
Route::post('/products/check-product', 'ProductController@checkProduct'); // 確認商品數量
Route::get('/products/{id}/shared-url', 'ProductController@sheredUrl'); // 縮短網址api

Route::resource('/admin/orders', 'Admin\OrderController'); // 後台訂單
Route::resource('/admin/products', 'Admin\ProductController'); // 後台產品
Route::post('/admin/products/upload-image', 'Admin\ProductController@uploadImage'); // 上傳圖片
Route::post('/admin/products/excel/import', 'Admin\ProductController@import'); // 上傳Excel
Route::post('/admin/orders/{id}/delivery', 'Admin\OrderController@delivery'); // 訂單送達通知
Route::get('/admin/orders/excel/export', 'Admin\OrderController@export'); // 匯出orders Excel
Route::get('/admin/orders/excel/export-by-shipped', 'Admin\OrderController@exportByShipped'); // 匯出分類orders Excel
Route::post('/admin/tools/update-product-price', 'Admin\ToolController@updateProductPrice');  // queue
Route::post('/admin/tools/create-product-redis', 'Admin\ToolController@createProductRedis');  // Redis

Route::post('/signup', 'AuthController@signup'); // 辦帳號
Route::post('/login', 'AuthController@login'); // 登陸
Route::group(['middleware'=>'auth:api'], function(){ // 對應到config/auth裡的guard下定義的驗證方式
    Route::get('/user', 'AuthController@user'); // 此用者
    Route::get('/logout', 'AuthController@logout'); // 登出
    Route::post('carts/checkout', 'CartController@checkout');  // 結帳
    Route::resource('/carts', 'CartController'); // 購物車
    Route::resource('/cart-items', 'CartItemsController'); // 購物車項目
});