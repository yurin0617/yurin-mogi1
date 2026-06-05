@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/setup.css') }}">
@endsection

@section('content')
<div class="profile-setup__content">

    {{-- プロフィール設定のタイトル --}}
    <h1 class="setup-title">プロフィール設定</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate class="setup-form">
        @csrf

        {{-- プロフィール画像エリア（左に画像、右にボタンを並べる箱） --}}
        <div class="form-group-image">
            <div class="image-flex-container">
                {{-- 左側の画像（丸形・#D9D9D9） --}}
                <div class="profile-image-container">
                    @if($user->profile && $user->profile->image_path)
                    <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="プロフィール画像" class="profile-icon">
                    @else
                    <div class="profile-icon-default">No Image</div>
                    @endif
                </div>

                {{-- 右側の「画像を選択する」ボタンに見せる仕組み --}}
                <div class="file-input-wrapper">
                    <label class="btn-select-image">
                        画像を選択する
                        <input type="file" name="image_path" class="hidden-file-input">
                    </label>
                    @error('image_path')
                    <p class="error-message-image">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label class="form-label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $defaultName) }}" class="form-input">
            @error('name')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label class="form-label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->profile->postal_code ?? '') }}" class="form-input">
            @error('postal_code')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label class="form-label">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->profile->address ?? '') }}" class="form-input">
            @error('address')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label class="form-label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->profile->building ?? '') }}" class="form-input">
        </div>

        {{-- 更新するボタン --}}
        <button type="submit" class="btn-submit-profile">更新する</button>
    </form>
</div>
@endsection