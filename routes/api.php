<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\StatsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/health', fn() => ['ok' => true]);

Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);

// Per-minute rate limit for classify endpoint
Route::post('/tickets/{id}/classify', [TicketController::class, 'classify'])
    ->middleware('throttle:ticket-classify');

Route::get('/stats', [StatsController::class, 'index']);