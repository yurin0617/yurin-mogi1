@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
    <p>メール認証を完了してください。</p>

    @if (session('status') == 'verification-link-sent')
    <p style="color: green;">新しい認証メールを再送信しました。</p>
    @endif

    <a href="http://localhost:8025" target="_blank" class="btn-verify">認証はこちらから</a>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-resend">認証メールを再送する</button>
    </form>
</div>
@endsection