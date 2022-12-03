<?php

use App\Http\Controllers\RequestController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('go', [RequestController::class, 'index']);
Route::post('go/store', [RequestController::class, 'store']);
Route::post('go/update', [RequestController::class, 'update']);
Route::post('go/delete', [RequestController::class, 'delete']);
