@extends('layout.app')
@section('content')
<link href="css/index.css" rel="stylesheet" type="text/css">
<table>
    <thead>
        <tr>
            <td>標題</td>
            <td>內容</td>
            <td>價格</td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->title }}</td>
            <td>{{ $product->content }}</td>
            <td>{{ $product->price }}</td>
            <td><input class="check_product" type="button" value="確認商品數量" data-id="{{ $product->id }}"></td>
            <td><input class="check_shared_url" type="button" value="分享商品" data-id="{{ $product->id }}"></td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('.check_product').on('click', function(){
        $.ajax({
            method: 'POST',
            url: '/products/check-product',
            data: {id: $(this).data('id')}
        })
        .done(function(response){
            if(response){
                alert("商品數量充足")
            }
            else{
                alert("商品數量不足")
            }
        })
    })

    $('.check_shared_url').on('click', function(){
        var id = $(this).data('id')
        $.ajax({
            method: 'GET',
            url: `/products/${id}/shared-url`,
        })
        .done(function(data){
            alert('請分享此縮短網址' + data.url)
        })
    })
</script>
@endsection