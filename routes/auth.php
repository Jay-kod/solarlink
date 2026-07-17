<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Legacy redirects for clean UX
    Route::get('register', function () {
        return redirect()->route('user.register');
    })->name('register');

    Route::get('login', function () {
        return redirect()->route('user.login');
    })->name('login');

    // Role-specific login pages
    Route::get('user/login', [AuthenticatedSessionController::class, 'create'])->name('user.login');
    Route::get('vendor/login', [AuthenticatedSessionController::class, 'create'])->name('vendor.login');
    Route::get('technician/login', [AuthenticatedSessionController::class, 'create'])->name('technician.login');
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');

    // Role-specific register pages
    Route::get('user/register', [RegisteredUserController::class, 'create'])->name('user.register');
    Route::get('vendor/register', [RegisteredUserController::class, 'create'])->name('vendor.register');
    Route::get('technician/register', [RegisteredUserController::class, 'create'])->name('technician.register');

    // Unified POST submit endpoints
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('user/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('user.logout');

    Route::post('technician/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('technician.logout');

    Route::post('vendor/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('vendor.logout');

    Route::post('admin/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('admin.logout');

    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
