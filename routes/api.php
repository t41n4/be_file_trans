<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\FileController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/checkToken', [AuthController::class, 'checkToken']);
// Route User
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/getfile', [FileController::class, 'getfile']);
    Route::get('/getuser', [FileController::class, 'getuser']);
    Route::post('/uploadfile', [FileController::class, 'uploadfile']);
    Route::post('/updatepublickey', [FileController::class, 'updatepublickey']);

});
Route::post('/log', [DeviceController::class, 'postLog']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
