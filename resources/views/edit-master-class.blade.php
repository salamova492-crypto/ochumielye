@extends('layouts.main')

@section('title', 'Редактировать мастер-класс')

@section('content')
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('cabinet.master-class.update', $masterClass->id) }}">
                @csrf
                @method('PUT')
                <h2>Редактирование мастер-класса</h2>

                @if($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <label>Вид творчества</label>
                    <input type="text" value="{{ $masterClass->creativityType->name }}" disabled>
                </div>
                <div class="form-group">
                    <label>Название мастер-класса</label>
                    <input type="text" value="{{ $masterClass->title }}" disabled>
                </div>
                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description" required>{{ old('description', $masterClass->description) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Дата</label>
                    <input type="date" value="{{ $masterClass->date->format('Y-m-d') }}" disabled>
                </div>
                <div class="form-group">
                    <label>Время</label>
                    <input type="text" value="{{ $masterClass->time_slot }}" disabled>
                </div>
                <div class="form-group">
                    <label>Количество человек в группе</label>
                    <input type="number" value="{{ $masterClass->maxPeople }}" disabled>
                </div>
                <div class="form-group">
                    <label>Стоимость мастер-класса (руб.)</label>
                    <input type="number" name="price" value="{{ old('price', $masterClass->price) }}" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <button class="btn">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
