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
    Route::apiResource('configurations', SettingController::class);
    Route::apiResource('loans', LoanController::class);
    Route::apiResource('copies', CopyController::class);
    Route::apiResource('genres', GenreController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('school-classes', SchoolClassController::class);
    Route::apiResource('users', UserController::class);
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
