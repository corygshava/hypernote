<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// stateless auth stuff
Route::prefix('/auth')->group(function (){
    Route::post('/login',[UserController::class,'login']);
    Route::post('/cur_user',[UserController::class,'get_cur_user']);
});
