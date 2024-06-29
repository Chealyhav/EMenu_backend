<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);


Route::get('/roles', [RoleController::class, 'getAllRoles']);
// GET /tests
Route::post('/tests', [TestController::class, 'store']);        // POST /tests
Route::get('/tests/{id}', [TestController::class, 'show']);     // GET /tests/{id}
Route::put('/tests/{id}', [TestController::class, 'update']);   // PUT /tests/{id}
Route::delete('/tests/{id}', [TestController::class, 'destroy']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

Route::get('/products', [ProductsController::class, 'index']);
Route::post('/products', [ProductsController::class, 'create']);
Route::put('/products/{id}', [ProductsController::class, 'update']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'getAllUser']);
    Route::get('/tests', [TestController::class, 'index']);
});


