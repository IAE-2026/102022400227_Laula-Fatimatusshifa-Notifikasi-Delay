<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DelayNotificationController;

Route::prefix('v1')
    ->middleware('iae.key')
    ->group(function () {

        Route::get('/trips/{id}/status', [DelayNotificationController::class, 'tripStatus']);

        Route::post('/delay-notifikasi', [DelayNotificationController::class, 'store']);

        Route::post('/delay-notifikasi/send', [DelayNotificationController::class, 'send']);

        Route::get('/delay-notifikasi/{id}', [DelayNotificationController::class, 'show']);
    });