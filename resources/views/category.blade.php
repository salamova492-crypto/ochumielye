@extends('layouts.main')

@section('title', $creativityType->name)

@section('content')
<div class="main">
    <div class="row">
        <div class="hover"></div>
        <div class="title">{{ $creativityType->name }}</div>
        <div class="row--small grid between">
            <div class="content">
                @if($creativityType->image)
                    <img src="{{ asset('img/' . $creativityType->image) }}">
                @endif
                @foreach(explode("\n\n", $creativityType->description) as $paragraph)
                    <p>{{ trim($paragraph) }}</p>
                @endforeach
            </div>
            <ul class="menu">
                @foreach($allCreativityTypes as $type)
                    <li><a href="{{ route('category.show', $type->id) }}">{{ $type->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="row shedule">
            <div class="row--small">
                <h2>Расписание</h2>
                <div class="drivers">
                    @forelse($creativityType->masterClasses as $masterClass)
                        <div class="driver grid">
                            <div class="driver-left grid">
                                <div class="driver-photo">
                                    <img src="{{ asset('img/' . $masterClass->leader->photo) }}">
                                </div>
                                <div class="driver-text">
                                    <div class="driver-name">{{ $masterClass->leader->fullName }}</div>
                                    <div class="driver-desc">{{ $masterClass->description }}</div>
                                    <div style="margin-top: 10px;">
                                        <strong>{{ $masterClass->title }}</strong><br>
                                        Свободных мест: {{ $masterClass->maxPeople - $masterClass->enrollments_count }} из {{ $masterClass->maxPeople }}<br>
                                        Стоимость: {{ number_format($masterClass->price, 0, '.', ' ') }} руб.
                                    </div>
                                </div>
                            </div>
                            <div class="driver-right">
                                @auth
                                    @if(auth()->user()->isVisitor())
                                        @if(!$masterClass->isFull())
                                            <a href="{{ route('enrollment.confirm', $masterClass->id) }}">
                                                <button class="driver-btn">Записаться</button>
                                            </a>
                                        @else
                                            <button class="driver-btn" disabled style="opacity: 0.5; cursor: not-allowed;">Мест нет</button>
                                        @endif
                                    @endif
                                @else
                                    <a href="{{ route('login') }}">
                                        <button class="driver-btn">Войти для записи</button>
                                    </a>
                                @endauth
                                <div class="driver-time">
                                    {{ $masterClass->date->translatedFormat('d F') }} {{ $masterClass->time_slot }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #fff; text-align: center;">Мастер-классы по данному виду творчества пока не запланированы.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
