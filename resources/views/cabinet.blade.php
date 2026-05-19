@extends('layouts.main')

@section('title', 'Личный кабинет')

@section('content')
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="hover"></div>
        <div class="title"></div>
        <div class="row--small grid between">
            <div class="content driver-page">
                <div class="driver-page-photo">
                    <img src="{{ asset('img/' . (auth()->user()->photo ?: 'driver-page.png')) }}">
                </div>
                <div class="driver-page-name">{{ auth()->user()->fullName }}</div>
                <div class="driver-page-text">
                    <div class="driver-page-my">Мои мастер-классы</div>
                    <table class="driver-page-table">
                        <tbody>
                            @forelse($masterClasses as $masterClass)
                                <tr>
                                    <td>
                                        {{ $masterClass->date->translatedFormat('d F') }} {{ $masterClass->time_slot }}<br>
                                        <small>{{ $masterClass->creativityType->name }}</small>
                                    </td>
                                    <td>
                                        <b>{{ $masterClass->title }}</b>
                                        <p>{{ $masterClass->description }}</p>
                                        <p>Стоимость: {{ number_format($masterClass->price, 0, '.', ' ') }} руб. | Мест: {{ $masterClass->maxPeople }}</p>
                                        @if($masterClass->enrollments->count() > 0)
                                            <p><strong>Участники:</strong></p>
                                            @foreach($masterClass->enrollments as $index => $enrollment)
                                                <p>
                                                    {{ $index + 1 }}. {{ $enrollment->user->fullName }}<br>
                                                    email: {{ $enrollment->user->email }}<br>
                                                    tel: {{ $enrollment->user->phone }}
                                                </p>
                                            @endforeach
                                        @else
                                            <p><em>Пока нет записанных участников</em></p>
                                        @endif
                                        <p>
                                            <a href="{{ route('cabinet.master-class.edit', $masterClass->id) }}">Редактировать</a>
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2"><em>У вас пока нет мастер-классов</em></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="driver-page-btn-wrapper">
                    <a href="{{ route('cabinet.master-class.create') }}" class="driver-page-btn btn">
                        Добавить мастер-класс
                    </a>
                </div>
            </div>
            <ul class="menu">
                @php
                    $creativityTypes = \App\Models\CreativityType::all();
                @endphp
                @foreach($creativityTypes as $type)
                    <li><a href="{{ route('category.show', $type->id) }}">{{ $type->name }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
