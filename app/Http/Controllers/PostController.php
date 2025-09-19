<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller{
    public function mekpost(Request $req){
    	$indata = $req->validate([
            'post_title' => ['required','min:3'],
            'post_message' => ['required','min:3','max:2000']
        ]);

        $san_title = strip_tags($indata['post_title']);
        $san_message = strip_tags($indata['post_message']);
        $san_uid = auth()->id();

        $outdata = [
        	'title' => $san_title,
        	'body' => $san_message,
        	'user_id' => $san_uid
        ];

        Post::create($outdata);

        return redirect('/')->with('success','post added successfully');
    }

    public function showEditScreen(Post $post){
        return view('edit-post', ['post' => $post]);
    }
}
