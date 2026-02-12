<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CopyController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('auth.jwt.cookie')->group(function () {
    Route::redirect('/', '/loans');

    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('view');
        Route::get('/filter', [LoanController::class, 'filter'])->name('filter');
        Route::get('/create-modal', [LoanController::class, 'createModal'])->name('create-modal');
        Route::get('/{loan}/update-modal', [LoanController::class, 'updateModal'])->name('update-modal');
        Route::get('/{loan}/menu-modal', [LoanController::class, 'menuModal'])->name('menu-modal');
        Route::get('/{loan}/extend-modal', [LoanController::class, 'extendModal'])->name('extend-modal');
    });

    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('view');
        Route::get('/filter', [BookController::class, 'filter'])->name('filter');
        Route::get('/create-modal', [BookController::class, 'createModal'])->name('create-modal');
        Route::get('/{book}/update-modal', [BookController::class, 'updateModal'])->name('update-modal');
        Route::get('/{book}/menu-modal', [BookController::class, 'menuModal'])->name('menu-modal');
    });

    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('view');
        Route::get('/filter', [StudentController::class, 'filter'])->name('filter');
        Route::get('/create-modal', [StudentController::class, 'createModal'])->name('create-modal');
        Route::get('/{student}/update-modal', [StudentController::class, 'updateModal'])->name('update-modal');
        Route::get('/{student}/menu-modal', [StudentController::class, 'menuModal'])->name('menu-modal');
    });

    Route::prefix('school-classes')->name('school-classes.')->group(function () {
        Route::get('/create-modal', [SchoolClassController::class, 'createModal'])->name('create-modal');
        Route::get('/{schoolClass}/update-modal', [SchoolClassController::class, 'updateModal'])->name('update-modal');
    });

    Route::prefix('genres')->name('genres.')->group(function () {
        Route::get('/create-modal', [GenreController::class, 'createModal'])->name('create-modal');
        Route::get('/{genre}/update-modal', [GenreController::class, 'updateModal'])->name('update-modal');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/create-modal', [UserController::class, 'createModal'])->name('create-modal');
        Route::get('/{user}/update-modal', [UserController::class, 'updateModal'])->name('update-modal');
        Route::get('/delete-modal', [UserController::class, 'deleteModal'])->name('delete-modal');
    });

    Route::prefix('copies')->name('copies.')->group(function () {
        Route::get('/{book}/manager-modal', [CopyController::class, 'managerModal'])->name('manager-modal');
        Route::get('/add-modal', [CopyController::class, 'addModal'])->name('add-modal');
    });

    Route::prefix('labels')->name('labels.')->group(function () {
        Route::get('/generate-modal', [LabelController::class, 'generateLabelModal'])->name('generate-modal');
    });
    Route::get('/labels/view/{file}', function (string $file) {
        abort_unless(request()->hasValidSignature(), 403);

        if (!Storage::disk('labels')->exists($file)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('labels')->path($file),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file . '"',
            ]
        );
    })->where('file', '.*')
        ->name('labels.view');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('view');
        Route::get('/genres', [SettingController::class, 'genres'])->name('genres');
        Route::get('/school-classes', [SettingController::class, 'schoolClasses'])->name('school-classes');
        Route::get('/users', [SettingController::class, 'users'])->name('users');
        Route::get('/config', [SettingController::class, 'config'])->name('config');
    });

    Route::get(
        '/modals/message',
        fn() =>
        view('components.modals.modal-message')
    )->name('modals.message');
});

Route::middleware('guest:web,api')->group(function () {
    Route::middleware('prevent.registration')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    });

    Route::middleware('users.exist')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

        Route::prefix('recover')->name('recovery.')->group(function () {
            Route::get('/', [PasswordRecoveryController::class, 'showSendRecoveryCodeForm'])
                ->middleware('code.not.sent')
                ->name('email.form');

            Route::get('/code', [PasswordRecoveryController::class, 'showRecoveryCodeValidationForm'])
                ->middleware('code.sent')
                ->name('code.form');

            Route::get('/new-password', [PasswordRecoveryController::class, 'showResetPasswordForm'])
                ->middleware('code.valid')
                ->name('new-password.form');
        });
    });
});
