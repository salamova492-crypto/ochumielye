<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use App\Models\MasterClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MasterClassController extends Controller
{
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['busySlots' => [], 'allBusy' => false]);
        }

        $busySlots = MasterClass::where('leader_id', Auth::id())
            ->whereDate('date', $date)
            ->pluck('time_slot')
            ->toArray();

        $allSlots = ['09:00-11:00', '11:00-13:00', '13:00-15:00', '15:00-17:00'];
        $allBusy = count($busySlots) === count($allSlots);

        return response()->json([
            'busySlots' => $busySlots,
            'allBusy' => $allBusy,
        ]);
    }

    public function create(): View
    {
        $creativityTypes = CreativityType::all();

        return view('create-master-class', compact('creativityTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'creativity_type_id' => 'required|exists:creativity_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time_slot' => 'required|in:09:00-11:00,11:00-13:00,13:00-15:00,15:00-17:00',
            'maxPeople' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ], [
            'creativity_type_id.required' => 'Выберите вид творчества',
            'title.required' => 'Заполните название мастер-класса',
            'description.required' => 'Заполните описание мастер-класса',
            'date.required' => 'Выберите дату проведения',
            'time_slot.required' => 'Выберите время проведения',
            'maxPeople.required' => 'Укажите количество человек в группе',
            'price.required' => 'Укажите стоимость мастер-класса',
        ]);

        $busySlot = MasterClass::where('leader_id', $user->id)
            ->whereDate('date', $validated['date'])
            ->where('time_slot', $validated['time_slot'])
            ->exists();

        if ($busySlot) {
            return back()->withErrors([
                'time_slot' => 'Выбранное время уже занято',
            ])->withInput();
        }

        MasterClass::create([
            'creativity_type_id' => $validated['creativity_type_id'],
            'leader_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'time_slot' => $validated['time_slot'],
            'maxPeople' => $validated['maxPeople'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('cabinet.index')->with('success', 'Мастер-класс успешно создан');
    }

    public function edit(int $id): View
    {
        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->leader_id !== Auth::id()) {
            abort(403, 'Вы можете редактировать только свои мастер-классы');
        }

        return view('edit-master-class', compact('masterClass'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->leader_id !== Auth::id()) {
            abort(403, 'Вы можете редактировать только свои мастер-классы');
        }

        $validated = $request->validate([
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ], [
            'description.required' => 'Заполните описание мастер-класса',
            'price.required' => 'Укажите стоимость мастер-класса',
        ]);

        $masterClass->update([
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('cabinet.index')->with('success', 'Мастер-класс успешно обновлён');
    }
}
