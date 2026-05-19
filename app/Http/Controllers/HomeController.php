<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $creativityTypes = CreativityType::withCount('masterClasses')->get();

        $myEnrollments = [];
        if (Auth::check()) {
            $myEnrollments = Enrollment::where('user_id', Auth::id())
                ->with(['masterClass.creativityType', 'masterClass.leader'])
                ->get();
        }

        return view('home', compact('creativityTypes', 'myEnrollments'));
    }
}
