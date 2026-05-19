@extends('layouts.main')

@section('title', 'Регистрация')

@section('content')
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <h2>Форма регистрации</h2>

                @if($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="fullName" value="{{ old('fullName') }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Подтверждение пароля</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <div class="form-group">
                    <label>Номер телефона</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required>
                </div>
                <div class="form-group">
                    <button class="btn">Отправить</button>
                </div>
                <div class="form-group">
                    <p>Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
