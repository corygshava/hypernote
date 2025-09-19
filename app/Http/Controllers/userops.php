<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class userops extends Controller{
    public function register(Request $req){
        $indata = $req->validate([
            "name" => ['required', 'min:3', 'max:16', Rule::unique('users', 'name')],
            "email" => ['required', 'email', Rule::unique('users', 'email')],
            "password" => ['required', 'min:5','max:32']
        ]);

        $upass = $indata['password'];
        $indata['password'] = bcrypt($upass);
        
        $user = User::create($indata);
        auth()->login($user);

        return redirect('/')->with('success','login successful');
    }

    public function logout(){
        auth()->logout();

        return redirect('/')->with('success','logout successful');
    }

    public function login(Request $req){
        $indata = $req->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $passdata = ['name' => $indata['username'],'password' => $indata['password']];

        if(auth()->attempt($passdata)){
            $req->session()->regenerate();
            return redirect('/')->with('success','login successful');
        } else {
            return redirect()->back()->withErrors([
                "name" => "incorrect login details"
            ]);
        }

    }
}
