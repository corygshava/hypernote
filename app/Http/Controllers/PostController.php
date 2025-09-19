<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller{
    public function mek_post(Request $req){
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
        // prevents unauthorised access
        if(auth()->user()->id !== $post['user_id']){
            return redirect('/')->with('danger',"you cant edit someone else\'s post");
        }

        return view('edit-post', ['post' => $post]);
    }

    public function update_post(Post $post, Request $req) {
        // prevents unauthorised access
        if(auth()->user()->id !== $post['user_id']){
            return redirect('/')->with('danger',"you cant edit someone else\'s post");
        }

        $indata = $req->validate([
            'post_title' => 'required',
            'post_message' => 'required'
        ]);

        $san_title = strip_tags($indata['post_title']);
        $san_message = strip_tags($indata['post_message']);

        $outdata = [
            'title' => $san_title,
            'body' => $san_message
        ];

        $post->update($outdata);

        return redirect('/')->with('success','Post updated successfully');
    }

    public function delete_post(Post $post){
        // prevents unauthorised access
        if(auth()->user()->id !== $post['user_id']){
            return redirect('/')->with('danger',"you cant delete someone else\'s post");
        }

        $post->delete();

        return redirect('/')->with('success','Post deleted successfully');
    }
}
