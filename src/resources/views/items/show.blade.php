@extends('layouts.app')


@section('content')

<img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" width="300">
<h1>{{ $item->name }}</h1>
<p>{{ $item->brand ?? 'ブランド情報なし' }}</p>
<p>¥{{ number_format($item->price) }}（税込）</p>

<div class="icon-group">
    @if($item->likes->where('user_id', auth()->id())->first())
    {{-- いいね済み：ピンクのハートを表示 --}}
    <form action="{{ route('like.destroy', $item->id) }}" method="POST" class="like-form">
        @csrf
        <button type="submit">
            <img src="{{ asset('images/heartlogo_pink.png') }}" alt="いいね解除" class="heart-icon">
        </button>
    </form>
    @else
    {{-- 未いいね：白抜きのハートを表示 --}}
    <form action="{{ route('like.store', $item->id) }}" method="POST" class="like-form">
        @csrf
        <button type="submit">
            <img src="{{ asset('images/heartlogo_default.png') }}" alt="いいねする" class="heart-icon">
        </button>
    </form>
    @endif

    {{-- カウント数 --}}
    <span class="count">{{ $item->likes->count() }}</span>
</div>

{{-- コメント数表示 --}}
<div class="icon-group">
    <img src="{{ asset('images/commentlogo.png') }}" alt="コメント数" class="icon">
    <span class="count">{{ $item->comments->count() }}</span>
</div>

<div class="item-action">
    @if($item->purchase)
    <button disabled style="background-color: gray;">売り切れました</button>
    @else
    <a href="{{ route('purchase.show', $item->id) }}" class="btn-purchase">購入手続きへ</a>
    @endif
</div>

<p>商品説明 {{ $item->description }}</p>
<p>商品の情報</p>
<p>カテゴリー
    @foreach($item->categories as $category)
    <span>{{ $category->name }}</span>
    @if(!$loop->last), @endif {{-- 最後の要素以外にカンマを表示 --}}
    @endforeach
</p>
<p>商品の状態 {{ $item->condition }}</p>

<div class="comment-section">
    <h2>コメント ({{ $item->comments->count() }})</h2>

    <div class="comments-list">
        @foreach($item->comments as $comment)
        <div class="comment-item">
            <div class="comment-user">
                <span>{{ $comment->user->name }}</span>
                @if($comment->user_id === $item->user_id)
                <span class="seller-badge">出品者</span>
                @endif
            </div>
            <div class="comment-comment">
                {{ $comment->comment }}
            </div>
            <div class="comment-time">
                {{ $comment->created_at->format('Y/m/d H:i') }}
            </div>
        </div>
        @endforeach
    </div>

    @auth
    {{-- ★ ここにメッセージ表示を追加！ --}}
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    @error('comment')
    <div class="alert alert-danger">
        {{ $message }}
    </div>
    @enderror

    <form action="{{ route('comment.store', $item->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea name="comment" class="form-control" rows="3" placeholder="商品へのコメントを入力してください"></textarea>
        </div>
        <button type="submit" class="btn-comment">コメントを送信する</button>
    </form>
    @else
    <p class="login-prompt"><a href="{{ route('login') }}">ログイン</a>するとコメントできます。</p>
    @endauth
</div>


@endsection