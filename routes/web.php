<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\LoyaltyPlanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoyaltyScanController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\LoyaltyTransactionController;
use App\Http\Controllers\PromoCodeController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('verified')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    // ADMIN ONLY
    // Later: user/staff management, system settings, reports, etc.
    Route::middleware('role:admin')->group(function () {

    Route::resource('users', UserManagementController::class)
        ->except(['show']);

});


    // ADMIN + MANAGEMENT
    Route::middleware('role:admin,management')->group(function () {

        Route::resource('services', ServiceController::class);

        Route::resource(
            'loyalty-plans',
            LoyaltyPlanController::class
        );

        Route::resource(
            'customers',
            CustomerController::class
        );

        Route::resource(
            'promo-codes',
            PromoCodeController::class
        )->except(['show']);

        Route::get('/transactions', [
            LoyaltyTransactionController::class,
            'index'
        ])->name('transactions.index');

        Route::get('/transactions/{transaction}', [
            LoyaltyTransactionController::class,
            'show'
        ])->name('transactions.show');

        Route::get('/memberships', [MembershipController::class, 'index'])
    ->name('memberships.index');

Route::get('/memberships/{membership}', [MembershipController::class, 'show'])
    ->name('memberships.show');

    });


    // ADMIN + MANAGEMENT + STAFF
    Route::middleware('role:admin,management,staff')->group(function () {

        Route::get('/scanner', [
            LoyaltyScanController::class,
            'index'
        ])->name('scanner.index');

        Route::post('/scanner/verify', [
            LoyaltyScanController::class,
            'verify'
        ])->name('scanner.verify');

        Route::post('/scanner/calculate', [
            LoyaltyScanController::class,
            'calculate'
        ])->name('scanner.calculate');

        Route::post('/scanner/confirm', [
            LoyaltyScanController::class,
            'confirm'
        ])->name('scanner.confirm');

    });

});

require __DIR__.'/auth.php';
