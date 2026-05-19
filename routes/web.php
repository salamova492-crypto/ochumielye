<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreativityTypeController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\MasterClassController;
use App\Http\Controllers\EnrollmentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/category/{id}', [CreativityTypeController::class, 'show'])->name('category.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/enrollment/{id}/confirm', [EnrollmentController::class, 'confirm'])->name('enrollment.confirm');
    Route::post('/enrollment/{id}', [EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::get('/enrollment/{id}/cancel', [EnrollmentController::class, 'cancel'])->name('enrollment.cancel');

    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet.index');

    Route::middleware(['leader'])->prefix('cabinet/master-class')->name('cabinet.master-class.')->group(function () {
        Route::get('/create', [MasterClassController::class, 'create'])->name('create');
        Route::get('/available-slots', [MasterClassController::class, 'getAvailableSlots'])->name('available-slots');
        Route::post('/', [MasterClassController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MasterClassController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MasterClassController::class, 'update'])->name('update');
    });
});
