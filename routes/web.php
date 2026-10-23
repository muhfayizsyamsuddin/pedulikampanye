<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'index'])
    ->name('chat.index');

Route::post('/chat', [ChatController::class, 'send'])
    ->middleware('throttle:10,1')
    ->name('chat.send');

Route::get('/conversations/{conversation}', [ChatController::class, 'show'])
    ->name('chat.show');