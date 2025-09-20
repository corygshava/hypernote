<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller{
    public function mek_post(Request $req){
    	$indata = $req->validate([
            'post_title' => ['required','min:3'],
            'post_message' => ['required','min:3','max:10000'],
            'privacy_s' => ['required','min:6']
        ]);

        $p_status = $indata['privacy_s'];
        $p_options = ['private', 'public', 'unlisted'];

        $san_title = strip_tags($indata['post_title']);
        $san_message = json_encode($indata['post_message']);
        $san_message = str_ireplace('script>', 'getfucked_hacker>', $san_message);
        $san_uid = auth()->id();
        $san_privacy = in_array($p_status,$p_options) ? $p_status : 'private';

        $outdata = [
        	'title' => $san_title,
        	'body' => $san_message,
        	'user_id' => $san_uid,
            'privacy_state' => $san_privacy
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
            'post_message' => 'required',
            'privacy_s' => ['required','min:6']
        ]);

        $p_status = $indata['privacy_s'];
        $p_options = ['private', 'public', 'unlisted'];

        $san_title = strip_tags($indata['post_title']);
        $san_message = json_encode($indata['post_message']);
        $san_message = str_ireplace('script>', 'getfucked_hacker>', $san_message);
        $san_privacy = in_array($p_status,$p_options) ? $p_status : 'private';

        $outdata = [
            'title' => $san_title,
            'body' => $san_message,
            'privacy_state' => $san_privacy
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

    public function show_public_posts(Post $post){
        // $allposts = Post::all();
        $allposts = Post::where('privacy_state', 'public')->get();

        return view('feed', ['posts' => $allposts]);
    }
}
