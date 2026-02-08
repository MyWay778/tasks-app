<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

// Apply rate limiting: 60 requests per minute per IP
Route::middleware('throttle:60,1')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
