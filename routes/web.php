<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public event browsing
Route::get('/', [EventController::class, 'home'])->name('home');
Route::get('/about', function(){
    return view('about');
})->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// Tracking
Route::get('/tracking', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [App\Http\Controllers\TrackingController::class, 'track'])->name('tracking.track');
Route::get('/tracking/{orderNumber}', [App\Http\Controllers\TrackingController::class, 'show'])->name('tracking.detail');
Route::get('/tracking/download-invoice/{orderNumber}', [App\Http\Controllers\TrackingController::class, 'downloadInvoice'])->name('tracking.download-invoice');

// Order creation and checkout
Route::get('/events/{slug}/order', [OrderController::class, 'create'])->name('orders.create');
Route::post('/events/{slug}/order', [OrderController::class, 'store'])->name('orders.store');
Route::get('/checkout/{token}', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/checkout/{token}/promo', [OrderController::class, 'applyPromo'])->name('orders.promo');
Route::get('/orders/{token}', [OrderController::class, 'show'])->name('orders.show');

// Payment status checks (AJAX)
Route::get('/orders/{token}/status', [OrderController::class, 'checkStatus'])->name('orders.status-check');
Route::post('/orders/{token}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

// Payment
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
