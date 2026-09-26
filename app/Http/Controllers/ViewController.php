<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller{
    public function show_my_notes(Request $req) {
        if(self::ili()){
            return view('profile.my_notes');
        } else {
            $this->showerror($req, 'log in first');
        }
    }

	public function showerror(Request $req,$msg = 'an error occured'){
		$dta = [
			'msg' => $msg,
		];

		return view('error',$dta);
	}
}
