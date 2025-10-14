<?php

use App\Http\Controllers\Api\Auth\PasswordRecoveryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\{
    StudentController,
    LoanController,
    CopyController,
    GenreController,
    BookController,
    SchoolClassController,
    SettingController,
    UserController
};

Route::prefix('auth')->group(function () {
    Route::middleware('prevent.registration')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('api.register');
    });

    Route::middleware('users.exist')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('api.login');
    });
});

Route::middleware(['auth.jwt.cookie'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::apiResource('students', StudentController::class);
    Route::get('/students/find-by-cpf/{cpf}', [StudentController::class, 'findByCpf'])
        ->name('students.findByCpf');

    Route::apiResource('genres', GenreController::class);

    Route::apiResource('books', BookController::class);
    Route::get('/books/find-by-isbn/{isbn}', [BookController::class, 'findByIsbn'])
        ->name('books.findByIsbn');

    Route::apiResource('school-classes', SchoolClassController::class);

    Route::apiResource('users', UserController::class);

    Route::apiResource('copies', CopyController::class)
        ->except(['update']);

    Route::apiResource('settings', SettingController::class)
        ->except(['store', 'destroy']);

    Route::apiResource('loans', LoanController::class)
        ->except(['update']);
    Route::patch('loans/{loan}/extend', [LoanController::class, 'extend'])
        ->name('loans.extend');
    Route::patch('loans/{loan}/finalize', [LoanController::class, 'finalize'])
        ->name('loans.finalize');
    Route::get('/loans/find-by-barcode/{barcode}', [LoanController::class, 'findByBarcode'])
        ->name('loans.findByBarcode');
});

Route::prefix('recover')->middleware('users.exist')->group(function () {
    Route::post('/send', [PasswordRecoveryController::class, 'sendCode'])
        ->middleware('code.not.sent')
        ->name('recover.send');

    Route::post('/resend', [PasswordRecoveryController::class, 'resendCode'])
        ->middleware('code.sent')
        ->name('recover.resend');

    Route::post('/code', [PasswordRecoveryController::class, 'validateCode'])
        ->middleware('code.sent')
        ->name('recover.validate.code');

    Route::post('/reset-password', [PasswordRecoveryController::class, 'resetPassword'])
        ->middleware('code.valid')
        ->name('recover.reset.password');
});
