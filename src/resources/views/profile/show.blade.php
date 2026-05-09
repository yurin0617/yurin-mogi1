@extends('layouts.app')

@section('content')
<div class="mypage-container">
    <div class="profile-info">
        {{-- プロフィール画像 --}}
        <div class="profile-image">
            @if($user->profile && $user->profile->image_path)
            <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="ユーザーアイコン" width="100">
            @else
            <div style="profile-icon-placeholder">No Image</div>
            @endif
        </div>

        {{-- ユーザー名 --}}
        <h2>{{ $user->name }}</h2>

        {{-- 編集画面へのリンク --}}
        <a href="{{ route('profile.setup') }}" class="btn">プロフィールを編集</a>
    </div>

    <hr>

    {{-- タブ切り替えメニュー --}}
    <div class="mypage-tabs">
        <a href="/mypage?page=sell" class="tab-item {{ $page === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>
        <a href="/mypage?page=buy" class="tab-item {{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- 商品グリッド一覧 --}}
    <div class="mypage-item-grid">
        @forelse($displayItems as $item)
        <div class="item-card">
            <a href="{{ route('item.show', $item->id) }}" class="item-link">
                <div class="item-image-wrapper">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </div>
                <p class="item-name">{{ $item->name }}</p>
            </a>
        </div>
        @empty
        <p class="empty-message">表示する商品がありません。</p>
        @endforelse
    </div>
    @endsection