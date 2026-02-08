<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::get('/{any}', function () {
    return view('app'); // Обычный blade-файл без @inertia
})->where('any', '.*');
