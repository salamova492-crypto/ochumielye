<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\MasterClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function confirm(int $id): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Для записи необходимо авторизоваться');
        }

        $masterClass = MasterClass::with(['creativityType', 'leader'])->findOrFail($id);

        if ($masterClass->isFull()) {
            return back()->with('error', 'Все места уже заняты');
        }

        if ($masterClass->isEnrolled(Auth::user())) {
            return back()->with('error', 'Вы уже записаны на этот мастер-класс');
        }

        $user = Auth::user();

        return view('enrollment-confirm', compact('masterClass', 'user'));
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Для записи необходимо авторизоваться');
        }

        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->isFull()) {
            return redirect()->route('category.show', $masterClass->creativity_type_id)
                ->with('error', 'Все места уже заняты');
        }

        if ($masterClass->isEnrolled(Auth::user())) {
            return redirect()->route('category.show', $masterClass->creativity_type_id)
                ->with('error', 'Вы уже записаны на этот мастер-класс');
        }

        if (Auth::user()->isLeader()) {
            return back()->with('error', 'Руководители не могут записываться на мастер-классы');
        }

        Enrollment::create([
            'master_class_id' => $masterClass->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('category.show', $masterClass->creativity_type_id)
            ->with('success', 'Вы успешно записаны на мастер-класс');
    }

    public function cancel(int $id): RedirectResponse
    {
        $masterClass = MasterClass::findOrFail($id);

        return redirect()->route('category.show', $masterClass->creativity_type_id)
            ->with('info', 'Запись на мастер-класс отменена');
    }
}
