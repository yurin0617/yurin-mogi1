<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>COACHTECH</title>
</head>

<body>
    <header>
        <img src="{{ asset('images/logo_COACHTECH.png')}}" alt="COACHTECH">

        {{-- 検索フォーム --}}
        <form action="/" method="GET">
            <input type="text" name="keyword" placeholder="なにをお探しですか？">
        </form>

        <nav>
            @if(auth()->check())
            {{-- ログイン時の表示 --}}
            <form action="/logout" method="POST" style="display:inline;">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="/mypage">マイページ</a>
            @else
            {{-- 未ログイン時の表示 --}}
            <a href="/login">ログイン</a>
            <a href="/register">会員登録</a>
            @endif
            <a href="/sell">出品</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>