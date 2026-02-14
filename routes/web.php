<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public event browsing
Route::get('/', [EventController::class, 'home'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/cities', [EventController::class, 'getCities'])->name('events.cities');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Order creation and checkout
Route::get('/events/{slug}/order', [OrderController::class, 'create'])->name('orders.create');
Route::post('/events/{slug}/order', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{orderToken}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/orders/{orderToken}/promo', [OrderController::class, 'applyPromo'])->name('orders.applyPromo');
Route::post('/orders/{orderToken}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/orders/{orderToken}/status', [OrderController::class, 'status'])->name('orders.status');
Route::get('/orders/{orderToken}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{orderToken}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

// Payment
Route::get('/orders/{orderToken}/payment', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->middleware('validate.webhook')
    ->name('payment.callback');
Route::get('/payment/finish/{orderToken}', [PaymentController::class, 'finish'])->name('payment.finish');

// Scanner Authentication
Route::get('/scanner/login', [\App\Http\Controllers\ScannerAuthController::class, 'showLogin'])->name('scanner.login');
Route::post('/scanner/login', [\App\Http\Controllers\ScannerAuthController::class, 'login'])->name('scanner.login.post');
Route::post('/scanner/logout', [\App\Http\Controllers\ScannerAuthController::class, 'logout'])->name('scanner.logout');

// Wristband Exchange (requires authentication + wristband_exchange_officer role)
Route::middleware(['auth', 'scanner.role'])->prefix('scanner/exchange')->name('scanner.exchange')->group(function () {
    Route::get('/', [\App\Http\Controllers\ScannerController::class, 'exchangeIndex']);
    Route::post('/scan', [\App\Http\Controllers\ScannerController::class, 'scanTicket'])->name('.scan');
    Route::post('/issue', [\App\Http\Controllers\ScannerController::class, 'issueWristband'])->name('.issue');
    Route::get('/history', [\App\Http\Controllers\ScannerController::class, 'exchangeHistory'])->name('.history');
});

// Wristband Validation (requires authentication + wristband_validator role)
Route::middleware(['auth', 'scanner.role'])->prefix('scanner/validate')->name('scanner.validate')->group(function () {
    Route::get('/', [\App\Http\Controllers\ScannerController::class, 'validateIndex']);
    Route::post('/scan', [\App\Http\Controllers\ScannerController::class, 'scanWristband'])->name('.scan');
    Route::post('/confirm', [\App\Http\Controllers\ScannerController::class, 'confirmEntry'])->name('.confirm');
    Route::get('/history', [\App\Http\Controllers\ScannerController::class, 'validateHistory'])->name('.history');
});
