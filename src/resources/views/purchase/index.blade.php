@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}">
@endsection

@section('content')
<div class="purchase-container">

    {{-- 📦 左側エリア：商品情報・支払い・配送先 --}}
    <div class="purchase-left">

        {{-- 商品基本情報 --}}
        <div class="purchase-item-info">
            <div class="item-image-box">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-text-box">
                <h2 class="item-name">{{ $item->name }}</h2>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <hr class="section-divider">

        {{-- 支払い方法 --}}
        <div class="purchase-section payment-section-block">
            <h1 class="section-title">支払い方法</h1>
            <div class="payment-select-box">
                <select name="payment_method" id="payment-select" class="payment-select-input" form="main-purchase-form">
                    <option value="">選択してください</option>
                    <option value="コンビニ払い" {{ session('payment_method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                    <option value="カード払い" {{ session('payment_method') == 'カード払い' ? 'selected' : '' }}>カード払い</option>
                </select>
                @error('payment_method')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="section-divider">

        {{-- 配送先 --}}
        <div class="purchase-section address-section-block">
            <div class="address-header">
                <h1 class="section-title">配送先</h1>
                <a href="{{ route('purchase.address.edit', $item->id) }}" class="btn-change">変更する</a>
            </div>
            <div class="address-content">
                <p class="postal-code">〒{{ $display_address['postal_code'] }}</p>
                <p class="address-text">{{ $display_address['address'] }}{{ $display_address['building'] }}</p>
            </div>
        </div>
        <hr class="section-divider">
    </div>

    {{-- 📦 右側エリア：確認枠・購入ボタン --}}
    <div class="purchase-right">

        <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="main-purchase-form" novalidate>
            @csrf

            {{-- 四角い確認枠 --}}
            <div class="purchase-summary-card">
                <div class="summary-item">
                    <span class="label">商品代金</span>
                    <span class="value">¥{{ number_format($item->price) }}</span>
                </div>

                <div class="summary-item">
                    <span class="label">支払い方法</span>
                    <span class="value" id="selected-payment">
                        {{ session('payment_method') ?? '未選択' }}
                    </span>
                </div>
            </div>

            {{-- 購入するボタン --}}
            <button type="submit" class="btn-submit-purchase">購入する</button>
        </form>

    </div>

</div>
@endsection