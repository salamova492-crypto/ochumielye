@extends('layouts.main')

@section('title', 'Главная')

@section('content')
<div class="main">
    <div class="row">
        <div class="hover"></div>
        <div class="title">ОчУмелые ручки</div>
        <div class="row--small grid between">
            <div class="content">
                <img src="{{ asset('img/logo.png') }}">
                <p>Добро пожаловать в клуб любителей творчества «ОчУмелые ручки»! Мы предлагаем уникальные мастер-классы по различным видам творчества.</p>
                <p>На наших занятиях вы сможете освоить новые навыки, раскрыть свой творческий потенциал и провести время в приятной компании единомышленников.</p>
                <p><span>Выберите интересующий вас вид творчества</span> в меню справа и запишитесь на ближайший мастер-класс!</p>
            </div>
            <ul class="menu">
                @foreach($creativityTypes as $type)
                    <li><a href="{{ route('category.show', $type->id) }}">{{ $type->name }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@if(auth()->check() && count($myEnrollments) > 0)
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <h2 class="my-enrollments-title">Мои записи</h2>
            <div class="my-enrollments-list">
                @foreach($myEnrollments as $enrollment)
                    <div class="enrollment-card">
                        <div class="enrollment-card-info">
                            <div class="enrollment-card-photo">
                                <img src="{{ asset('img/' . $enrollment->masterClass->leader->photo) }}">
                            </div>
                            <div class="enrollment-card-text">
                                <div class="driver-name">{{ $enrollment->masterClass->leader->fullName }}</div>
                                <div class="driver-desc">{{ $enrollment->masterClass->creativityType->name }}</div>
                                <strong>{{ $enrollment->masterClass->title }}</strong><br>
                                Стоимость: {{ number_format($enrollment->masterClass->price, 0, '.', ' ') }} руб.
                            </div>
                        </div>
                        <div class="enrollment-card-time">
                            {{ $enrollment->masterClass->date->translatedFormat('d F') }}<br>
                            {{ $enrollment->masterClass->time_slot }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection
