<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->isLeader()) {
                return redirect()->route('cabinet.index');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль',
        ])->withInput($request->only('email'));
    }

    public function showRegisterForm(): View
    {
        return view('register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\(\) ]+$/'],
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'visitor',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
