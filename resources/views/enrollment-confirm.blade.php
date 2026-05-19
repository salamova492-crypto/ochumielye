@extends('layouts.main')

@section('title', 'Подтверждение записи')

@section('content')
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <h2 style="text-align: center; padding: 30px 0;">Подтверждение записи на мастер-класс</h2>

            <div style="background: #fff; padding: 30px; margin-bottom: 30px; border-radius: 5px;">
                <p><strong>ФИО:</strong> {{ $user->fullName }}</p>
                <p><strong>Вид творчества:</strong> {{ $masterClass->creativityType->name }}</p>
                <p><strong>Мастер:</strong> {{ $masterClass->leader->fullName }}</p>
                <p><strong>Название:</strong> {{ $masterClass->title }}</p>
                <p><strong>Дата:</strong> {{ $masterClass->date->translatedFormat('d F Y') }}</p>
                <p><strong>Время:</strong> {{ $masterClass->time_slot }}</p>
                <p><strong>Стоимость:</strong> {{ number_format($masterClass->price, 0, '.', ' ') }} руб.</p>
            </div>

            <div style="text-align: center;">
                <form method="POST" action="{{ route('enrollment.store', $masterClass->id) }}" style="display: inline;">
                    @csrf
                    <button class="btn" style="margin-right: 20px;">Подтвердить запись</button>
                </form>
                <a href="{{ route('enrollment.cancel', $masterClass->id) }}" class="btn">Отмена</a>
            </div>
        </div>
    </div>
</div>
@endsection
