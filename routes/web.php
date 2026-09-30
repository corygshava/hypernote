<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('/ui')->group(function () {
    Route::get('/my_notes', [ViewController::class, 'show_my_notes']);
    Route::get('/{p}', [ViewController::class, 'showme']);
});

Route::prefix('/data')->group(function () {
    Route::get('/my_notes',[PostController::class, 'get_my_notes']);
});

Route::prefix('/op')->group(function (){
    Route::post('/add_edit_note',[PostController::class, 'add_edit_note']);
});

Route::prefix('/data/stats')->group(function () {
    Route::get('/my_notes',[PostController::class, 'get_my_notes_stats']);
});

require __DIR__.'/auth.php';
