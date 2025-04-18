<header class="header">
    <div class="header-container">
        <h1 class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/images/コーチテックlogo.svg') }}" alt="フリマアプリのロゴ">
            </a>
        </h1>

        @if (!(request()->routeIs('login') || request()->routeIs('register')))
            <form action="{{ route('items.index') }}" method="GET" class="search-form" id="header-search-form">
                <input
                    type="text"
                    name="keyword"
                    placeholder="なにをお探しですか？"
                    value="{{ request('keyword') }}"
                >
                <button type="submit" class="hidden-button">検索</button>
            </form>

            <nav class="nav">
                <ul class="nav-list">
                    @guest
                        <li><a href="{{ route('login') }}" class="nav-link">ログイン</a></li>
                        <li><a href="{{ route('mypage.index') }}" class="nav-link">マイページ</a></li>
                        <li><a href="#" class="nav-link post-button">出品</a></li>
                    @else
                        <li>
                            <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="nav-link">
                                ログアウト
                            </a>
                        </li>
                        <li><a href="{{ route('mypage.index') }}" class="nav-link">マイページ</a></li>
                        <li><a href="#" class="nav-link post-button">出品</a></li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </nav>
        @endif
    </div>
</header>
