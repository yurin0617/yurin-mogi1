@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form action="/sell" method="POST" enctype="multipart/form-data" novalidate class="sell-form">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-group">
            <label class="form-label-main">商品画像</label>
            <div class="image-upload-wrapper">
                <label class="btn-select-image">
                    画像を選択する
                    <input type="file" name="image" class="hidden-file-input">
                </label>
            </div>
            @error('image')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品の詳細セクション --}}
        <div class="section-group">
            <h2 class="section-heading">商品の詳細</h2>

            {{-- カテゴリー --}}
            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <div class="category-tags-container">
                    @foreach($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }} class="hidden-checkbox">
                        <span class="tag-text">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('category_ids')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 💡 商品の状態（Figma完全再現カスタムプルダウン） --}}
            <div class="form-group">
                <label class="form-label">商品の状態</label>

                {{-- 実際にサーバーに値を送るための隠し入力欄 --}}
                <input type="hidden" name="condition" id="hidden-condition" value="{{ old('condition') }}">

                <div class="custom-select-container">
                    {{-- クリックするボタン部分 --}}
                    <div class="custom-select-trigger" id="select-trigger">
                        <span id="selected-text">{{ old('condition') ?: '選択してください' }}</span>
                        <span class="custom-arrow">▼</span>
                    </div>

                    {{-- 💡 飛び出る選択肢メニュー（基本背景色: #636769） --}}
                    <ul class="custom-options-list" id="options-list">
                        <li class="custom-option {{ old('condition') == '良好' ? 'selected' : '' }}" data-value="良好">
                            <span class="checkmark">✓</span>良好
                        </li>
                        <li class="custom-option {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}" data-value="目立った傷や汚れなし">
                            <span class="checkmark">✓</span>目立った傷や汚れなし
                        </li>
                        <li class="custom-option {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}" data-value="やや傷や汚れあり">
                            <span class="checkmark">✓</span>やや傷や汚れあり
                        </li>
                        <li class="custom-option {{ old('condition') == '状態が悪い' ? 'selected' : '' }}" data-value="状態が悪い">
                            <span class="checkmark">✓</span>状態が悪い
                        </li>
                    </ul>
                </div>

                @error('condition')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 商品名と説明セクション --}}
        <div class="section-group">
            <h2 class="section-heading">商品名と説明</h2>

            <div class="form-group">
                <label class="form-label">商品名</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input">
                @error('name')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">ブランド名</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="form-input">
                @error('brand')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">商品の説明</label>
                <textarea name="description" rows="6" class="form-textarea">{{ old('description') }}</textarea>
                @error('description')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">販売価格</label>
                <div class="price-input-wrapper">
                    <span class="price-currency">¥</span>
                    <input type="number" name="price" min="0" value="{{ old('price') }}" class="form-input price-input">
                </div>
                @error('price')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn-submit-sell">出品する</button>
    </form>
</div>

{{-- 💡 プルダウンを動かすためのJavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('select-trigger');
        const list = document.getElementById('options-list');
        const hiddenInput = document.getElementById('hidden-condition');
        const selectedText = document.getElementById('selected-text');
        const options = document.querySelectorAll('.custom-option');

        // 1. ボタンをクリックしたらメニューを開閉する
        trigger.addEventListener('click', function(e) {
            e.stopPropagation(); // クリックイベントが外側に広がるのを防ぐ
            list.classList.toggle('open');
        });

        // 2. 選択肢（良好、状態が悪いなど）をクリックしたときの処理
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');

                // 一度すべての選択肢から「selected」クラスを外す
                options.forEach(opt => opt.classList.remove('selected'));

                // 今クリックした選択肢に「selected」クラスをつける（これで青背景＆チェックマークが付く！）
                this.classList.add('selected');

                // 画面に見えている文字を更新
                selectedText.textContent = value;

                // サーバーに送る用の隠し入力欄（hidden）に値をセット
                hiddenInput.value = value;

                // メニューを閉じる
                list.classList.remove('open');
            });
        });

        // 3. メニューが開いているときに、画面の他の場所をクリックしたら自動で閉じるお守り
        document.addEventListener('click', function() {
            list.classList.remove('open');
        });
    });
</script>
@endsection