<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CopyController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt.cookie')->group(function () {
    Route::get('/', [LoanController::class, 'index'])->name('loans.view');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.view');
    Route::get('/loans/filter', [LoanController::class, 'filter'])->name('loans.filter.view');
    Route::get('/loans/create-modal', [LoanController::class, 'createModal'])->name('loans.createModal');
    Route::get('/loans/{loan}/update-modal', [LoanController::class, 'updateModal'])->name('loans.updateModal');
    Route::get('/loans/{loan}/menu-modal', [LoanController::class, 'menuModal'])->name('loans.menuModal');
    Route::get('/loans/{loan}/extend-modal', [LoanController::class, 'extendModal'])->name('loans.extendModal');

    Route::get('/books', [BookController::class, 'index'])->name('books.view');
    Route::get('/books/filter', [BookController::class, 'filter'])->name('books.filter.view');
    Route::get('/books/create-modal', [BookController::class, 'createModal'])->name('books.createModal');
    Route::get('/books/{book}/update-modal', [BookController::class, 'updateModal'])->name('books.updateModal');
    Route::get('/books/{book}/menu-modal', [BookController::class, 'menuModal'])->name('books.menuModal');

    Route::get('/students', [StudentController::class, 'index'])->name('students.view');
    Route::get('/students/filter', [StudentController::class, 'filter'])->name('students.filter.view');
    Route::get('/students/create-modal', [StudentController::class, 'createModal'])->name('students.createModal');
    Route::get('/students/{student}/update-modal', [StudentController::class, 'updateModal'])->name('students.updateModal');
    Route::get('/students/{student}/menu-modal', [StudentController::class, 'menuModal'])->name('students.menuModal');

    Route::get('/copies/{book}/manager-modal', [CopyController::class, 'managerModal'])->name('copies.managerModal');
    Route::get('/labels/generate-modal', [LabelController::class, 'generateLabelModal'])->name('labels.generateModal');

    Route::get('/modals/modal-message', function () {
        return view('components.modals.modal-message');
    })->name('modals.modalMessage');

    Route::get('/settings/modal', function () {
        return view('settings.index');
    })->name('settings.view');
});

Route::middleware('guest')->group(function () {
    Route::middleware('prevent.registration')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])
            ->name('register.form')
        ;
    });

    Route::middleware('users.exist')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

        Route::prefix('recover')->group(function () {
            Route::get('/', [PasswordRecoveryController::class, 'showSendRecoveryCodeForm'])
                ->middleware('code.not.sent')
                ->name('recovery.email.form')
            ;

            Route::get('/code', [PasswordRecoveryController::class, 'showRecoveryCodeValidationForm'])
                ->middleware('code.sent')
                ->name('recovery.code.form')
            ;

            Route::get('/new-password', [PasswordRecoveryController::class, 'showResetPasswordForm'])
                ->middleware('code.valid')
                ->name('recovery.new-password.form')
            ;
        });
    });
});
