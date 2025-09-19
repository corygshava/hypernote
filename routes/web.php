<?php

use Illuminate\Support\Facades\Route;

use App\Models\Post;

use App\Http\Controllers\userops;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    // $allposts = Post::all(); // gets everything
    // $allposts = Post::where('user_id', auth()->id())->get(); // gets everything but with a catch

    $allposts = [];
    if(auth()->check()){
        $allposts = auth()->user()->userposts()->latest()->get();
    }

    return view('home',['posts' => $allposts]);
});

// user session routes
Route::post('/register', [userops::class,'register']);
Route::post('/login', [userops::class,'login']);
Route::post('/logout', [userops::class,'logout']);

// Blog post routes
Route::post('/mek-post', [PostController::class,'mekpost']);
Route::get('/edit-post/{post}', [PostController::class,'showEditScreen']);