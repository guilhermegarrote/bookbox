<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\PasswordRecoveryController;

Route::middleware('auth.jwt.cookie')->group(function () {
    Route::get('/', [LoanController::class, 'index'])
        ->name('loans.view');
    Route::get('/loans', [LoanController::class, 'index'])
        ->name('loans.view');

    Route::get('/books', [BookController::class, 'index'])
        ->name('books.view');

    Route::get('/students', [StudentController::class, 'index'])
        ->name('students.view');
    Route::get('/students/filter', [StudentController::class, 'filter'])->name('students.filter.view');
    Route::get('/students/create-modal', [StudentController::class, 'createModal'])->name('students.createModal');
    Route::get('/students/{student}/menu-modal', [StudentController::class, 'menuModal'])->name('students.menuModal');
});

Route::middleware('guest')->group(function () {
    Route::middleware('prevent.registration')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])
            ->name('register.form');
    });

    Route::middleware('users.exist')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

        Route::prefix('recover')->group(function () {
            Route::get('/', [PasswordRecoveryController::class, 'showSendRecoveryCodeForm'])
                ->middleware('code.not.sent')
                ->name('recovery.email.form');

            Route::get('/code', [PasswordRecoveryController::class, 'showRecoveryCodeValidationForm'])
                ->middleware('code.sent')
                ->name('recovery.code.form');

            Route::get('/new-password', [PasswordRecoveryController::class, 'showResetPasswordForm'])
                ->middleware('code.valid')
                ->name('recovery.new-password.form');
        });
    });
});
