@extends('layout.app')
@section('content')
<h3>聯絡我們</h3>
<form action="">
    請問你是: <input name="name" type="text"> <br>
    請問你的消費時間: <input name="date" type="date"> <br>
    你消費的商品種類:
    <select name="product" id="">
        <option value="物品">物品</option>
        <option value="食物">食物</option>
    </select>
    <button class="btn btn-primary">送出</button>
</form>
@endsection
