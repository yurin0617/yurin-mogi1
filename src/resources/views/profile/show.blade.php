@extends('layouts.app')

@section('content')
<div class="mypage-container">
    <div class="profile-info">
        {{-- プロフィール画像 --}}
        <div class="profile-image">
            @if($user->profile && $user->profile->image_path)
            <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="ユーザーアイコン" width="100">
            @else
            <div style="width:100px; height:100px; background:#ccc;">No Image</div>
            @endif
        </div>

        {{-- ユーザー名 --}}
        <h2>{{ $user->name }}</h2>

        {{-- 編集画面へのリンク --}}
        <a href="{{ route('profile.setup') }}" class="btn">プロフィールを編集</a>
    </div>

    <hr>

    <div class="tabs">
        <p>出品した商品 / 購入した商品（今後実装）</p>
    </div>
</div>
@endsection