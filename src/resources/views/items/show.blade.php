@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
<div class="item-detail__container">

    {{-- 📸 左側エリア：商品画像のみ --}}
    <div class="item-detail__left">
        <div class="item-image__box">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="main-image">
        </div>
    </div>

    {{-- 📝 右側エリア：詳細情報やコメントすべて --}}
    <div class="item-detail__right">

        <h1 class="item-name">{{ $item->name }}</h1>
        <p class="item-brand">{{ $item->brand ?? 'ブランド情報なし' }}</p>
        <p class="item-price">¥{{ number_format($item->price) }}（税込）</p>

        {{-- いいね・コメントアイコンエリア --}}
        <div class="item-stats">
            <div class="stat-box">
                @if($item->likes->where('user_id', auth()->id())->first())
                <form action="{{ route('like.destroy', $item->id) }}" method="POST" class="like-form">
                    @csrf
                    <button type="submit" class="icon-btn">
                        <img src="{{ asset('images/heartlogo_pink.png') }}" alt="いいね解除" class="heart-icon">
                    </button>
                </form>
                @else
                <form action="{{ route('like.store', $item->id) }}" method="POST" class="like-form">
                    @csrf
                    <button type="submit" class="icon-btn">
                        <img src="{{ asset('images/heartlogo_default.png') }}" alt="いいねする" class="heart-icon">
                    </button>
                </form>
                @endif
                <span class="count">{{ $item->likes->count() }}</span>
            </div>

            <div class="stat-box">
                <img src="{{ asset('images/commentlogo.png') }}" alt="コメント数" class="icon">
                <span class="count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        {{-- 購入ボタン --}}
        <div class="item-action">
            @if($item->purchase)
            <button class="btn-soldout" disabled>売り切れました</button>
            @else
            <a href="{{ route('purchase.show', $item->id) }}" class="btn-purchase">購入手続きへ</a>
            @endif
        </div>

        {{-- 商品説明 --}}
        <div class="info-section">
            <h2 class="section-title">商品説明</h2>
            <p class="description-text">{{ $item->description }}</p>
        </div>

        {{-- 商品の情報 --}}
        <div class="info-section">
            <h2 class="section-title">商品の情報</h2>
            <table class="info-table">
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        @foreach($item->categories as $category)
                        <span class="category-tag">{{ $category->name }}</span>
                        @if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>商品の状態</th>
                    <td>{{ $item->condition }}</td>
                </tr>
            </table>
        </div>

        {{-- コメントエリア --}}
        <div class="comment-section">
            <h2 class="section-title comment-title">コメント ({{ $item->comments->count() }})</h2>

            <div class="comments-list">
                @foreach($item->comments as $comment)
                <div class="comment-item">
                    <div class="comment-user-info">
                        <div class="comment-avatar">
                            @if($comment->user->profile && $comment->user->profile->image_path)
                            <img src="{{ asset('storage/' . $comment->user->profile->image_path) }}" alt="プロフィール画像">
                            @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルト画像">
                            @endif
                        </div>
                        <span class="comment-username">{{ $comment->user->name }}</span>
                        @if($comment->user_id === $item->user_id)
                        <span class="seller-badge">出品者</span>
                        @endif
                    </div>
                    <div class="comment-content-box">
                        <div class="comment-comment">{{ $comment->comment }}</div>
                        <div class="comment-time">{{ $comment->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            @auth
            @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            @error('comment')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <form action="{{ route('comment.store', $item->id) }}" method="POST" class="comment-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">商品へのコメント</label>
                    <textarea name="comment" class="form-control" rows="4" placeholder="商品へのコメントを入力してください"></textarea>
                </div>
                <button type="submit" class="btn-comment">コメントを送信する</button>
            </form>
            @else
            <p class="login-prompt"><a href="{{ route('login') }}">ログイン</a>するとコメントできます。</p>
            @endauth
        </div>

    </div>
</div>
@endsection