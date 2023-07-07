<?php

use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('members.guard')->group(function () {
    Route::post('/login', [MemberAuthController::class, 'login']);
    Route::post('/register', [MemberController::class, 'register']);
    Route::middleware('api.refresh')->group(function () {
        // auth
        Route::post('/logout', [MemberAuthController::class, 'logout']);

        // book
        Route::get('/books', [BookController::class, 'list']);
        Route::get('/book/{id}', [BookController::class, 'one']);
        Route::post('/book', [BookController::class, 'store']);
        Route::put('/book/{id}', [BookController::class, 'edit']);
        Route::delete('/book/{id}', [BookController::class, 'delete']);
    });
});
