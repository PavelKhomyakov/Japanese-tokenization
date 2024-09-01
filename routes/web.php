<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TextAnalyzerController;

Route::get('/', function () {
    return view('analyze');
});

Route::post('/analyze', [TextAnalyzerController::class, 'analyze'])->name('analyze');
