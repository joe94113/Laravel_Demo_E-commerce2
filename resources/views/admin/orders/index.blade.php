@extends('layout.admin_app')
@section('content')
<link href="{{ asset('css\index.css') }}" rel="stylesheet">
<h2>後台-訂單列表</h2>
<sapn>訂單總數: {{ $orderCount }}</sapn>
<div>
    <a class="a_nav" href="/admin/orders/excel/export">匯出訂單 Excel</a><br>
    <a class="a_nav" href="/admin/orders/excel/export-by-shipped">匯出分類訂單 Excel</a>
</div>
<table>
    <thead>
        <tr>
            <td>購買時間</td>
            <td>購買者</td>
            <td>商品清單</td>
            <td>訂單總額</td>
            <td>是否運送</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td>{{ $order->created_at }}</td>
            <td>{{ $order->user->name }}</td>
            <td>
                @foreach($order->orderItems as $orderItems)
                    {{ $orderItems->product->title }} &nbsp;
                @endforeach
            </td>
            <td>{{ isset($order->orderItems) ? $order->orderItems->sum('price') : 0}}</td>
            <td>{{ $order->is_shipped ? "是" : "否"}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div>
    @for ($i = 1; $i <= $orderPages; $i++)
        <a class="a_nav" href="/admin/orders?page={{ $i }}">第 {{ $i }} 頁</a>
    @endfor
</div>
@endsection