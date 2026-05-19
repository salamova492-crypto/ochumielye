<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MasterClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CabinetController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user->isLeader()) {
            abort(403, 'Доступ только для ведущих мастер-классов');
        }

        $masterClasses = MasterClass::where('leader_id', $user->id)
            ->with(['creativityType', 'enrollments.user'])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->get();

        return view('cabinet', compact('masterClasses'));
    }
}
