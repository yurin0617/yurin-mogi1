@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}">
@endsection

@section('content')
<div class="address-edit__content">

    {{-- 💡 ご希望の「住所の変更」タイトルを真ん中上に追加 --}}
    <h1 class="address-title">住所の変更</h1>

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
        @csrf

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label class="form-label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" class="form-input">
            @error('postal_code')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label class="form-label">住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address) }}" class="form-input">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label class="form-label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $profile->building) }}" class="form-input">
        </div>

        {{-- 住所変更フォームの中にこれを忍ばせておく --}}
        <input type="hidden" name="payment_method" value="{{ session('payment_method') }}">

        {{-- 更新するボタン --}}
        <button type="submit" class="btn-submit-address">更新する</button>
    </form>
</div>
@endsection