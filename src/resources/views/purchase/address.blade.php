<form action="{{ route('purchase.address.update', $item->id) }}" method="POST">
    @csrf

    <label>郵便番号</label>
    <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}">
    @error('postal_code')
    <p style="color: red;">{{ $message }}</p>
    @enderror

    <label>住所</label>
    <input type="text" name="address" value="{{ old('address', $profile->address) }}">
    @error('address')
    <p style="color: red;">{{ $message }}</p>
    @enderror

    {{-- 建物名は任意 --}}
    <label>建物名</label>
    <input type="text" name="building" value="{{ old('building', $profile->building) }}">

    <button type="submit">更新する</button>
</form>