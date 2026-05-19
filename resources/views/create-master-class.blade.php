@extends('layouts.main')

@section('title', 'Добавить мастер-класса')

@section('content')
<div class="row row--nogutter">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form method="POST" action="{{ route('cabinet.master-class.store') }}" id="masterClassForm" novalidate>
                @csrf
                <h2>Форма добавления мастер-класса</h2>

                @if($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group" style="position: relative;">
                    <label>Вид творчества</label>
                    <select name="creativity_type_id" required class="{{ $errors->has('creativity_type_id') ? 'input-error' : '' }}">
                        <option value="">Выберите вид творчества</option>
                        @foreach($creativityTypes as $type)
                            <option value="{{ $type->id }}" {{ old('creativity_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Название мастер-класса</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="{{ $errors->has('title') ? 'input-error' : '' }}">
                </div>
                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description" required class="{{ $errors->has('description') ? 'input-error' : '' }}">{{ old('description') }}</textarea>
                </div>
                <div class="form-group" style="position: relative;">
                    <label>Дата</label>
                    <input type="date" name="date" id="dateInput" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}" class="{{ $errors->has('date') ? 'input-error' : '' }}">
                    <div id="date-warning" style="display: none; color: #d9534f; font-size: 14px; margin-top: 5px;">Эта дата полностью занята. Выберите другую.</div>
                </div>
                <div class="form-group">
                    <label>Время</label>
                    <select name="time_slot" id="timeSlotSelect" required>
                        <option value="">Сначала выберите дату</option>
                        <option value="09:00-11:00">09:00 - 11:00</option>
                        <option value="11:00-13:00">11:00 - 13:00</option>
                        <option value="13:00-15:00">13:00 - 15:00</option>
                        <option value="15:00-17:00">15:00 - 17:00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Количество человек в группе</label>
                    <input type="number" name="maxPeople" value="{{ old('maxPeople') }}" min="1" required class="{{ $errors->has('maxPeople') ? 'input-error' : '' }}">
                </div>
                <div class="form-group">
                    <label>Стоимость мастер-класса (руб.)</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required class="{{ $errors->has('price') ? 'input-error' : '' }}">
                </div>
                <div class="form-group">
                    <button class="btn" id="submitBtn" disabled>Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .input-error {
        border-color: #d9534f !important;
        box-shadow: 0 0 5px rgba(217, 83, 79, 0.5);
    }
    #dateInput.date-busy {
        border-color: #d9534f !important;
        background-color: #fdf0f0;
    }
</style>

<script>
    const dateInput = document.getElementById('dateInput');
    const timeSlotSelect = document.getElementById('timeSlotSelect');
    const submitBtn = document.getElementById('submitBtn');
    const dateWarning = document.getElementById('date-warning');
    const form = document.getElementById('masterClassForm');

    const allSlots = [
        { value: '09:00-11:00', label: '09:00 - 11:00' },
        { value: '11:00-13:00', label: '11:00 - 13:00' },
        { value: '13:00-15:00', label: '13:00 - 15:00' },
        { value: '15:00-17:00', label: '15:00 - 17:00' },
    ];

    function loadSlots(date) {
        dateWarning.style.display = 'none';
        dateInput.classList.remove('date-busy');

        if (!date) {
            resetSlots();
            return;
        }

        timeSlotSelect.innerHTML = '<option value="">Загрузка...</option>';
        timeSlotSelect.disabled = true;
        submitBtn.disabled = true;

        fetch('{{ route('cabinet.master-class.available-slots') }}?date=' + date)
            .then(response => response.json())
            .then(data => {
                timeSlotSelect.innerHTML = '';
                timeSlotSelect.disabled = false;

                if (data.allBusy) {
                    dateWarning.style.display = 'block';
                    dateInput.classList.add('date-busy');

                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Все слоты заняты на эту дату';
                    option.selected = true;
                    option.disabled = true;
                    timeSlotSelect.appendChild(option);
                    submitBtn.disabled = true;
                    return;
                }

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Выберите время';
                placeholder.disabled = true;
                timeSlotSelect.appendChild(placeholder);

                const oldTimeSlot = '{{ old('time_slot') }}';

                allSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value;
                    option.textContent = slot.label;

                    if (data.busySlots.includes(slot.value)) {
                        option.disabled = true;
                        option.textContent = slot.label + ' (занято)';
                        option.style.color = '#999';
                    }

                    if (slot.value === oldTimeSlot) {
                        option.selected = true;
                    }

                    timeSlotSelect.appendChild(option);
                });

                submitBtn.disabled = false;
            })
            .catch(() => {
                timeSlotSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                timeSlotSelect.disabled = false;
                submitBtn.disabled = false;
            });
    }

    function resetSlots() {
        timeSlotSelect.innerHTML = '<option value="">Сначала выберите дату</option>';
        timeSlotSelect.disabled = false;
        submitBtn.disabled = true;
    }

    dateInput.addEventListener('change', function () {
        loadSlots(this.value);
    });

    if (dateInput.value) {
        loadSlots(dateInput.value);
    }

    form.addEventListener('submit', function (e) {
        if (!dateInput.value) {
            e.preventDefault();
            dateInput.classList.add('input-error');
            dateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endsection
