<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller{
    public $viewmap = [
        'my_notes' => 'profile.my_notes',
    ];

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

    public function showme(Request $req,$p){
        if(!(isset($this->viewmap[$p]))){
            return $this->showerror($req, 'invalid UI request');
        }

        $view = $this->viewmap[$p];
        return view($view);
    }
}
