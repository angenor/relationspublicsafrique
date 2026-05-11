<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Annuaire — endpoints publics (rate-limit 60/min/IP)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/annuaire', [\App\Http\Controllers\AnnuaireController::class, 'apiIndex'])->name('api.annuaire.index');
    Route::get('/annuaire/{slug}', [\App\Http\Controllers\AnnuaireController::class, 'apiShow'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('api.annuaire.show');
});
