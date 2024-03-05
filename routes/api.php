<?php

use App\Http\Controllers\Api\LikeCommentApi;
use App\Http\Controllers\Api\PostControllerApi;
use App\Http\Controllers\AuthController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

  
Route::post('login', [AuthController::class, 'login']);
Route::get('/posts', [PostControllerApi::class, 'index']);
Route::get('/posts/{id}', [PostControllerApi::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('/post', [PostControllerApi::class, 'store']);//multiple images field name is images[]
    Route::put('/post/{id}', [PostControllerApi::class, 'update']);
    Route::delete('/post/{id}', [PostControllerApi::class, 'destroy']);

    Route::post('/post/{post}/like', [LikeCommentApi::class, 'like']);

    Route::post('/post/{post}/comment', [LikeCommentApi::class, 'store']);
    Route::put('/comment/{comment}', [LikeCommentApi::class, 'update']);
    Route::delete('/comment/{comment}', [LikeCommentApi::class, 'destroy']);
    Route::post('/post/{post}/comment/{comment}/approve', [LikeCommentApi::class, 'approve']);
});

     