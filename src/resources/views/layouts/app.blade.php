<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header class="header">
        <div class="header-left">
            <a href="/">
                <img src="{{ asset('images/logo_COACHTECH.png')}}" alt="COACHTECH">
            </a>
        </div>

        <!-- ログイン・会員登録画面「以外」の時だけ表示する -->
        @unless(Route::is('login') || Route::is('register'))
        {{-- 検索フォーム --}}
        <div class="header-center">
            <form action="/" method="GET" class="search-form">
                {{-- 現在のタブ情報を隠し持って送る --}}
                <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="search-input" placeholder="なにをお探しですか？">
            </form>
        </div>

        <nav class="header-right">
            <ul class="nav-list">
                @if (Auth::check())
                {{-- ログイン時の表示 --}}
                <li>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="nav-button">ログアウト</button>
                    </form>
                </li>
                <li><a href="/mypage" class="nav-link">マイページ</a></li>
                @else
                {{-- 未ログイン時の表示 --}}
                <li><a href="/login" class="nav-link">ログイン</a></li>
                <li><a href="/mypage" class="nav-link">マイページ</a></li>
                @endif
                <li><a href="/sell" class="nav-link sell-button">出品</a></li>
            </ul>
        </nav>
        @endunless
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>