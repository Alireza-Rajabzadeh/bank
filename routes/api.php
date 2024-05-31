<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardsController;

Route::group(['prefix' => '/client/', 'middleware' => 'auth'], function () {

    Route::group(['prefix' => '/cards/'], function () {

        Route::post('/', [CardsController::class, 'addCard'])->name('add-card');
    });
});
