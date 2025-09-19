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
            "password" => ['required', 'min:8','max:32']
        ]);

        $upass = $indata['password'];
        $indata['password'] = bcrypt($upass);
        
        $user = User::create($indata);
        auth()->login($user);

        return redirect('/');
    }

    public function logout(){
        auth()->logout();

        return redirect('/');
    }
}
