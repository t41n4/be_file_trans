<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\DeviceController;
use App\Models\Role;
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
// Route Public API
Route::post('/login', [AuthController::class, 'login']);


// Route User
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/getAllUser', [UserController::class, 'index']);
    Route::get('/getSpecifyUser', [UserController::class, 'getbyid']);
    Route::put('/updateUser', [UserController::class, 'edit']);
    Route::delete('/deleteUser', [UserController::class, 'destroy']);
    Route::post('/insertUser', [UserController::class, 'create']);
    // get my profile
    Route::get('/getMyProfile', [UserController::class, 'getMyProfile']);

});
Route::post('/log', [DeviceController::class, 'postLog']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
