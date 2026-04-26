<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>商品を出品する</title>
</head>

<body>
    <h1>商品の出品</h1>

    {{-- ここがデータ送信の肝になります --}}
    <form action="/sell" method="POST" enctype="multipart/form-data">
        @csrf
        <p>商品画像: <input type="file" name="image"></p>
        <h2>商品の詳細</h2>
        <p>カテゴリー（複数選択可）:
            @foreach($categories as $category)
            <label>
                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                {{ $category->name }}
            </label>
            @endforeach
        </p>
        <p>商品の状態:
            <select name="condition">
                <option value="">選択してください</option>
                {{-- oldの値とoptionのvalueが一致したら selected をつける --}}
                <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
            </select>
        </p>
        <h2>商品名と説明</h2>
        <p>商品名: <input type="text" name="name" value="{{ old('name') }}"></p>
        <p>ブランド名: <input type="text" name="brand" value="{{ old('brand') }}"></p>
        <p>商品の説明: <textarea name="description">{{ old('description') }}</textarea></p>
        <p>販売価格: <input type="number" name="price" value="{{ old('price') }}"></p>

        <button type="submit">出品する</button>
    </form>
    @if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</body>

</html>