@extends('layouts.app')

@section('content')
<div class="profile-setup__content">
    <h1>プロフィール設定</h1>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="form-group">
            <label>プロフィール画像</label>

            <div class="profile-image-container">
                @if($user->profile && $user->profile->image_path)
                <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="プロフィール画像" class="profile-icon">
                @else
                <div class="profile-icon-default">No Image</div>
                @endif
            </div>

            <input type="file" name="image_path">

            @error('image_path')
            <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        {{-- 名前（初期値に現在の名前を入れる） --}}
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $defaultName) }}">
            @error('name')
            <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div>
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->profile->postal_code ?? '') }}">
            @error('postal_code')
            <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        {{-- 住所 --}}
        <div>
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->profile->address ?? '') }}">
            @error('address')
            <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div>
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->profile->building ?? '') }}">
        </div>

        <button type="submit">更新する</button>
    </form>
</div>
@endsection