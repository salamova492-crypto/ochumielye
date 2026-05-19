<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ОчУмелые ручки')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
</head>
<body>
    <div class="header">
        <div class="row grid middle between">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="Логотип"></a>
            </div>
            <div class="title">
                Клуб любителей творчества «ОчУмелые ручки»
            </div>
            <div class="auth">
                @auth
                    @if(auth()->user()->isLeader())
                        <a href="{{ route('cabinet.index') }}">Личный кабинет</a>
                    @endif
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}">Вход</a>
                @endauth
            </div>
        </div>
    </div>
    <div class="row row--nogutter">
        <div class="menu-burger">
            <div class="burger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="row row--nogutter flash-message" id="flashSuccess">
            <div class="row--small">
                <div style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="row row--nogutter flash-message" id="flashError">
            <div class="row--small">
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="row row--nogutter flash-message" id="flashInfo">
            <div class="row--small">
                <div style="background: #d1ecf1; color: #0c5460; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; text-align: center;">
                    {{ session('info') }}
                </div>
            </div>
        </div>
    @endif

    <script>
        setTimeout(function() {
            const flash = document.querySelector('.flash-message');
            if (flash) {
                flash.style.transition = 'opacity 0.5s';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }
        }, 4000);
    </script>

    @yield('content')

    <div class="footer">
        <div class="row">
            <div class="row--small grid between">
                <div class="address">Наш адрес: ВДНХ, 120в</div>
                <div class="tel">Тел: 89123456765</div>
                <div class="copy">(с) Copyright, 2017</div>
            </div>
        </div>
    </div>
</body>
</html>
