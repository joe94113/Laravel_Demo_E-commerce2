<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Exports\OrdersMultipleExport;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\OrderDelivery;
use Maatwebsite\Excel\Facades\Excel;

class Ordercontroller extends Controller
{
    public function index(Request $request)
    {
        $orderCount = Order::whereHas('orderItems')->count();  // 訂單總數
        $dataPerPage = 2;
        $orderPages = ceil($orderCount / $dataPerPage);
        $currentPage = isset($request->all()['page']) ? $request->all()['page'] : 1;
        $orders = Order::with(['user', 'orderItems.product'])->orderBy('created_at', 'desc')
                        ->offset($dataPerPage * ($currentPage - 1))
                        ->limit($dataPerPage)
                        ->whereHas('orderItems')
                        ->get();
        return view('admin.orders.index', ['orders' => $orders,
                                            'orderCount' => $orderCount,
                                            'orderPages' => $orderPages]);                             
    }

    public function delivery($id){
        $order = Order::find($id);
        if($order->is_shipped){
            return response(['result' => false]);
        }else{
            $order->update(['is_shipped' => true]);
            $order->user->notify(new OrderDelivery);
            return response(['result' => true]);
        }
    }

    public function export() // 匯出Excel
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }

    public function exportByShipped()
    {
        return Excel::download(new OrdersMultipleExport, 'orders_by_shipped.xlsx');
    }
}
