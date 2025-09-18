<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class userops extends Controller{
    public function register(Request $req){
        $indata = $req->validate([
            "name" => 'required|min:3',
            "email" => ['required', 'email'],
            "password" => ['required', 'min:8','max:32']
        ]);

        return 'Hello from our controller';
    }
}
