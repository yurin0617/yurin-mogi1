<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>{{ $item->name }}の詳細</title>
</head>

<body>
    <h1>{{ $item->name }}</h1>
    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" width="300">
    <p>ブランド: {{ $item->brand ?? 'ブランド情報なし' }}</p>
    <p>¥{{ number_format($item->price) }}（税込）</p>
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
</body>

</html>