@extends('layouts.app')

@section('content')
<h1>商品一覧ページ</h1>

{{-- タブの切り替えエリア --}}
<div class="tabs">
    {{-- keywordも一緒に渡すことで、検索したままタブを切り替えられるようになります --}}
    <a href="/?tab=all&keyword={{ $keyword }}">
        おすすめ
    </a>
    <a href="/?tab=mylist&keyword={{ $keyword }}">
        マイリスト
    </a>
</div>

<div class="item-list">
    {{-- コントローラーから送られてきた $items をループで表示します --}}
    @foreach($items as $item)
    <div class="item">
        <a href="{{ route('item.show', $item->id) }}">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">

            {{-- 2. ここにSOLDのロジックを追加！ --}}
            @if($item->purchase()->exists())
            <div class="sold-badge">
                <span>SOLD</span>
            </div>
            @endif
    </div>

    <p>{{ $item->name }}</p>
    </a>
</div>
@endforeach
</div>
@endsection