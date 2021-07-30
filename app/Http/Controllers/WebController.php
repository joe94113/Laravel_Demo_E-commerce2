<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;  // laravel內建notification 模型

class WebController extends Controller
{
    public $notifications = [];  // 預先建立notifications，不然每個
    public function __construct()
    {
        $user = User::find(2);
        $this->notifications = $user->notifications ?? [];
    }

    public function index()
    {
        $products = Product::all();
        return view('web.index', ['products' => $products, 'notifications' => $this->notifications]);
    }

    public function contactUs()
    {
        return view('web.contact_us', ['notifications' => $this->notifications]);
    }

    public function readNotifications(Request $request)
    {
        $id = $request->all()['id'];
        DatabaseNotification::find($id)->markAsRead(); // 標記已讀

        return response(['result' => true]);
    }
}
