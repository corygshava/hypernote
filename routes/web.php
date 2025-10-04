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
        $allposts = auth()->user()->userposts()->latest()->paginate(12);;
    }

    return view('home',['posts' => $allposts]);
});

// user session routes
Route::post('/register', [userops::class,'register']);
Route::post('/login', [userops::class,'login']);
Route::post('/logout', [userops::class,'logout']);

// Blog post routes
Route::post('/mek-post', [PostController::class,'mek_post']);
Route::get('/edit-post/{post}', [PostController::class,'showEditScreen']);
Route::put('/edit-post/{post}', [PostController::class,'update_post']);
Route::delete('/delete-post/{post}', [PostController::class,'delete_post']);

// feed requests
Route::get('/posts', [PostController::class, 'show_public_posts']);
Route::get('/feed', [PostController::class, 'show_public_posts']);

