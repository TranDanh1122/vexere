<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web'])->name('app.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.vi');
    Route::get('/tra-cuu', [HomeController::class, 'findSlot'])->name('tracuu.vi');
    Route::get('/ve-xe-khach-tu-sai-gon-di-ba-ria-vung-tau.html', [PublicController::class, 'getSlotSgVt'])->name('getslotsgvt.vi');
    Route::get('/ve-xe-khach-tu-ba-ria-vung-tau-di-sai-gon.html', [PublicController::class, 'getSlotVtSg'])->name('getslotvtsg.vi');
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('find-slots', [PublicController::class, 'findSlots'])->name('find-slots');
        Route::post('place-order', [PublicController::class, 'placeOrder'])->name('placeOrder');
        Route::post('custormer-slot', [PublicController::class, 'customerSlot'])->name('customerSlot');
        Route::get('/download-ticket/{code}', [PublicController::class, 'download'])->name('ticket.download');
    });
});


Route::get('test', [HomeController::class, 'test'])->name('test')->middleware(['web', 'auth-admin', '2fa']);
