<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
</head>

<body>
    <h1>商品一覧ページ</h1>

    {{-- ここがログイン状態によって表示を変える場所です --}}
    @auth
    <p>ログイン中です！</p>
    @else
    <p>ゲストさん、こんにちは！</p>
    @endauth

    <div class="item-list">
        {{-- コントローラーから送られてきた $items をループで表示します --}}
        @foreach($items as $item)
        <div class="item">
            <a href="{{ route('item.show', $item->id) }}">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" width="100">
                <p>{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>
</body>

</html>