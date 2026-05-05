@extends('layouts.app')

@section('content')

{{-- 簡易的な購入画面の例 --}}
<h1>購入手続き</h1>

<div>
    <h3>商品情報</h3>
    <p>商品名: {{ $item->name }}</p>
    <p>価格: ¥{{ number_format($item->price) }}</p>
</div>

<div>
    <h3>配送先住所</h3>
    <p>〒{{ auth()->user()->profile->postal_code }}</p>
    <p>{{ auth()->user()->profile->address }} {{ auth()->user()->profile->building }}</p>
    <a href="{{ route('purchase.address.edit', $item->id) }}">住所を変更する</a>
</div>

<form action="{{ route('purchase.store', $item->id) }}" method="POST" novalidate>
    @csrf
    <h3>支払い方法</h3>
    <select name="payment_method" required>
        <option value="">選択してください</option>
        <option value="コンビニ払い">コンビニ払い</option>
        <option value="カード払い">カード払い</option>
    </select>

    <button type="submit" style="margin-top: 20px;">購入を確定する</button>
</form>

@endsection