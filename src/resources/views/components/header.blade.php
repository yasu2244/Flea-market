<header class="header">
    <div class="header-container">
        <h1 class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/images/コーチテックlogo.svg') }}" alt="フリマアプリのロゴ">
            </a>
        </h1>

        @if (!(request()->routeIs('login') || request()->routeIs('register')))
            <nav class="nav">
                <ul class="nav-list">
                    @guest
                        <li><a href="{{ route('login') }}" class="nav-link">ログイン</a></li>
                        <li><a href="{{ route('register') }}" class="nav-link">会員登録</a></li>
                        <li><a href="#" class="nav-link">マイページ</a></li>
                        <li><a href="#" class="nav-link">出品する</a></li>
                    @else
                        <li><a href="#" class="nav-link">マイページ</a></li>
                        <li><a href="#" class="nav-link">出品する</a></li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="nav-link">
                                ログアウト
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </nav>
        @endif
    </div>
</header>
