<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\TransactionController;

Route::group(['prefix' => '/client/', 'middleware' => 'auth'], function () {

    Route::group(['prefix' => '/cards/'], function () {
        Route::post('/', [CardsController::class, 'addCard'])->name('add-card');
        Route::post('/transaction', [TransactionController::class, 'doTransaction'])->name('do-transaction');
    });
});


Route::group(['prefix' => '/transactions', 'middleware' => 'auth'], function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions-list');
    Route::get('/users    ', [TransactionController::class, 'transactionalUsers'])->name('transactional-users');
});
