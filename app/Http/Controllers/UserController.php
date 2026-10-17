<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{
    public function login(Request $req) {
        $res = $this->dftres;
		$alldata = $req->all();
		$msg = "didnt even get to start";
		$out = null;

		// /*
		$passed = $req->validate([
			'email' => ['required','email'],
			'password' => ['required']
		]);
		// */

		// utils
			$update_runlog = function($wot) use (&$res){
				$res['runlog'][] = $wot;
			};
			$update_msg = function($wot,$logit = true) use (&$msg,&$update_runlog){
				$msg = $wot;
				if($logit){
					$update_runlog($wot);
				}
			};
			$_msg = function($w,$l = true) use (&$update_msg){
				$update_msg($w,$l);
			};
			$_rlg = function($w) use (&$update_runlog){
				$update_runlog($w);
			};

		// ops begin
		if(!($this->ili())){
			$res['success'] = true;
			$update_msg('starting the procedure');
			$reslt = false;

			$email = $passed['email'];
			$pw = $passed['password'];
			$newpw = null;
			$enc = self::$encdata;

			$postpro = [
				'email' => $email,
				'password' => EncController::decryptme($pw, $enc['o'], $enc['s'])
			];
			$reslt = Auth::attempt($postpro);

			if(!$reslt){
				$_msg('invalid credentials');
				$user = User::query()->where('email',$email)->with('myRoles')->first();

				if($user == null){
					$_msg('user not found');
				} else {
					// $wegood = $this->verifyPassword($pw,$newpw,$user);
					$wegood = $this->verifyPassword($pw, $newpw, $user);
					// $logdata['uname'] = $user->name;
					// $logdata['user_id'] = $user->id;

					if(!$wegood){
						$_msg('invalid password');
					} else {
						$_msg('this shouldnt happen, tell Cory immediately!');
					}
				}
			} else {
				$_msg("valid user details, creating token");
				$user = Auth::user();
				$session_lifetime_days = 14;
				$expiry = now()->addDays($session_lifetime_days);
				$token = $user->createToken('api-token',['*'], $expiry)->plainTextToken;
				$out = [
					'token' => $token,
					'expiry' => $expiry,
				];
				$_msg('remote login successful');
				// $reslt = false;
			}

			// $cmdt = self::commondata();

			$res['result'] = $reslt;
			$res['sendme'] = $out;
			$res['message'] = $msg;
		} else {
			$res['message'] = "log out first";
			$res['success'] = true;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
    }

	public function get_cur_user(Request $req) {
		$res = $this->dftres;
		$alldata = $req->all();
		$msg = "didnt even get to start";
		$out = null;

		/*
		$passed = $req->validate([
			'condition' => ['required']
		]);
		// */

		// utils
			$update_runlog = function($wot) use (&$res){
				$res['runlog'][] = $wot;
			};
			$update_msg = function($wot,$logit = true) use (&$msg,&$update_runlog){
				$msg = $wot;
				if($logit){
					$update_runlog($wot);
				}
			};
			$_msg = function($w,$l = true) use (&$update_msg){
				$update_msg($w,$l);
			};
			$_rlg = function($w) use (&$update_runlog){
				$update_runlog($w);
			};

		// ops begin
		if($this->ili()){
			$res['success'] = true;
			$update_msg('starting the procedure');
			$reslt = false;

			// $cmdt = self::commondata();
			$out = self::cur_user();
			$_msg('User retrieved successfully');
			$reslt = $out != null;

			$res['result'] = $reslt;
			$res['sendme'] = $out;
			$res['message'] = $msg;
		} else {
			$_msg('session not found');
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}
}
