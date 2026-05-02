@extends('layouts.app')


@section('content')

<img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" width="300">
<h1>{{ $item->name }}</h1>
<p>{{ $item->brand ?? 'ブランド情報なし' }}</p>
<p>¥{{ number_format($item->price) }}（税込）</p>

<div class="like-area">
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
    <span>{{ $item->likes->count() }}</span>
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


<a href="/">一覧に戻る</a>
@endsection