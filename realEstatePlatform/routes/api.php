<?php

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




use App\Http\Controllers\SignupController;

Route::post('/signup', [SignupController::class, 'signup']);







use App\Http\Controllers\AppUserController;

Route::post('/wardrob/signup', [AppUserController::class, 'account_creation']);
Route::post('/wardrob/login', [AppUserController::class, 'login']);
Route::post('/wardrob/google_login', [AppUserController::class, 'google_login']);
Route::post('/wardrob/password_reset', [AppUserController::class, 'password_reset']);
Route::middleware('auth:sanctum')->post('/wardrob/logout', [AppUserController::class, 'logout']);




use App\Http\Controllers\ClothItemController;
Route::middleware('auth:sanctum')->get('/wardrob/info', [ClothItemController::class, 'accountDetails']);
Route::middleware('auth:sanctum')->get('/wardrob/cloth', [ClothItemController::class, 'my_cloth']);
Route::middleware('auth:sanctum')->post('/wardrob/upload', [ClothItemController::class, 'create']);
Route::middleware('auth:sanctum')->get('/wardrob/search', [ClothItemController::class, 'clothSearch']);
Route::middleware('auth:sanctum')->post('/wardrob/like/{id}', [ClothItemController::class, 'like']);
Route::middleware('auth:sanctum')->post('/wardrob/update/{id}', [ClothItemController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/wardrob/delete/{id}', [ClothItemController::class, 'destroy']);


