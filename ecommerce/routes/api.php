<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::apiResource('/categories', App\Http\Controllers\Api\CategoryController::class);
Route::apiResource('/products', App\Http\Controllers\Api\ProductController::class);
Route::get('/categories/{id}/products', [App\Http\Controllers\Api\CategoryController::class, 'getproductcategory']);

// ========== auth api
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// /////////////////////////////
Route::group(["middleware"=>"auth:sanctum"],function(){
    Route::get( '/profile', [AuthController::class, 'profile']);
    Route::get( '/logout', [AuthController::class, 'logout']);

});