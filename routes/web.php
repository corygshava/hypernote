<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userops;

Route::get('/', function () {
    return view('home');
});

Route::post('/register', [userops::class,'register']);
Route::post('/logout', [userops::class,'logout']);