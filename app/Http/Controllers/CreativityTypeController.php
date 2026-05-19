<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use Illuminate\View\View;

class CreativityTypeController extends Controller
{
    public function show(int $id): View
    {
        $creativityType = CreativityType::with(['masterClasses' => function ($query) {
            $query->with(['leader', 'creativityType'])
                ->withCount('enrollments')
                ->orderBy('date')
                ->orderBy('time_slot');
        }])->findOrFail($id);

        $allCreativityTypes = CreativityType::all();

        return view('category', compact('creativityType', 'allCreativityTypes'));
    }
}
