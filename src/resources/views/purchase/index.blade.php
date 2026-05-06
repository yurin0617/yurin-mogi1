@extends('layouts.app')

@section('content')

<div>
    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    <p>{{ $item->name }}</p>
    <p> ¥{{ number_format($item->price) }}</p>
</div>

<form action="{{ route('purchase.store', $item->id) }}" method="POST" novalidate>
    @csrf
    <h1>支払い方法</h1>
    <select name="payment_method" required>
        <option value="">選択してください</option>
        <option value="コンビニ払い">コンビニ払い</option>
        <option value="カード払い">カード払い</option>
    </select>
    @error('payment_method')
    <div style="color: red;">{{ $message }}</div>
    @enderror

    <div>
        <h1>配送先</h1>
        <p>〒{{ $display_address['postal_code'] }}</p>
        <p>{{ $display_address['address'] }}{{ $display_address['building'] }}</p>
        <a href="{{ route('purchase.address.edit', $item->id) }}">変更する</a>
    </div>

    <div class="purchase-summary">
        <!-- 商品代金の表示 -->
        <div class="summary-item">
            <span class="label">商品代金</span>
            <span class="value">¥{{ number_format($item->price) }}</span>
        </div>

        <!-- 支払い方法の表示 -->
        <div class="summary-item">
            <span class="label">支払い方法</span>
            <span class="value" id="selected-payment">
                {{-- セッションやリクエストに保存された支払い方法があれば表示、なければ「未選択」 --}}
                {{ session('payment_method') ?? '未選択' }}
            </span>
        </div>
    </div>

    <button type="submit" style="margin-top: 20px;">購入する</button>
</form>

@endsection