<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController; 


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [AuthController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('todos', TodoController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});



//mysql çevir 
// kullanıcı tabanlı todo moddeller arası ilişki kurmak için user_id ekleyelim ve ilişkileri tanımlayalım.
//log eklencek ve loglama yapılacak işlemler belirlenecek. (örneğin: todo oluşturma, güncelleme, silme gibi işlemler loglanabilir)