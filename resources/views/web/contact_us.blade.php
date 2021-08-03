@extends('layout.app')
@section('content')
<form class="w-50" action="">
  <div class="mb-3">
    <label for="" class="form-label">請問你是:</label>
    <input type="text" class="form-control" name="name" aria-describedby="">
  </div>
  <div class="mb-3">
    <label for="" class="form-label">請問您消費的時間</label>
    <input type="date" name="date" class="form-control" id="exampleInputPassword1">
  </div>
  <div class="mb-3">
      <label for="disabledSelect" class="form-label">您消費種類:</label>
      <select name="product" id="disabledSelect" class="form-select">
        <option value="物品">物品</option>
        <option value="食物">食物</option>
      </select>
    </div>
  <button type="submit" class="btn btn-primary">送出</button>
</form>
@endsection

