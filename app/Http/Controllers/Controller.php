<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Location\Facades\Location;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\userRoles;
use App\Models\systemData;
use App\Models\User;

use App\Http\Controllers\FileopsController;

abstract class Controller{
	public static $system_commission_date = '2026-02-02 12:44:35';
	public static $system_completion_date = '2026-02-17 12:44:35';

	public static $prefix = '_assets';
	public static $uimode = "dark";
	public static $sitedata = null;
	public static $permissions = [];
	public static $mypermissions = [];
	public static $universal_start_date = '2026-02-13 03:12:29';
	public static $system_data_cutoff = '2026-06-24 00:12:29';
	public static $sitedata_last_update = '2026-09-07 17:33:29';
	public static $sys_permissions_last_update = '2026-03-05 12:44:35';
	public static $system_start_date = '2026-06-23 00:00:35';

	// storage stuff
	public static $payload_path = "runtime/payloads/";
	public static $bizfiles_path = "runtime/bizinfo/";
	public static $report_files_path = "runtime/report_files/";
	public static $report_images_path = "runtime/report_images/";
	public static $invoice_files_path = "runtime/invoice_files/";
	public static $invoice_images_path = "runtime/invoice_images/";

	// Api stuff
	public static $fetchId = 'viaFetch';
	public static $exchage_rates_url = 'https://api.exchangerate-api.com/v4/latest/USD';

	// response shape stuff
	public static $showlogs = true;
	public $dftres = [
		"success" => false,
		'result' => false,
		"message" => "authentication Error, try again",
		"sendme" => null,
		"runlog" => []
	];

	// shared methods start
	public static function mekRandomString($charcount = 7, $useNums = true, $useSymbols = false) {
		// setup character sets
		$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*()^~`";
		if (!$useSymbols) {
			$letters = substr($letters, 0, 26); // A-Z only
		}

		$lettersLo = strtolower($letters);
		$nums = "1234578906";

		$options = $letters . $lettersLo;
		if ($useNums) {
			$options .= $nums;
		}

		$res = '';
		$len = strlen($options);

		for ($i = 0; $i < $charcount; $i++) {
			$res .= $options[random_int(0, $len - 1)];
		}

		return $res;
	}

	public static function isadmin(){
		$res = false;
		$utype = "normal";

		if(Auth::check()){
			$urole = Auth::user()->myRoles;
			$rolename = $urole == null ? '' : $urole->role_name;

			if(strtolower($rolename) == 'admin'){
				$res = true;
			}
		}

		// echo "$utype";

		return $res;
	}

	public static function isloggedin(){
		$auth = Auth::check();

		if(!$auth){
			$auth = Auth::guard('sanctum')->check();
		}

		return $auth;
	}

	public static function ili() {
		return self::isloggedin();
	}

	public static function cur_user() : User | null{
		if(self::ili()){
			$user = Auth::user();

			if($user == null){
				$user = Auth::guard('sanctum')->user();
			}

			return $user;
		} else {
			return null;
		}
	}

	public static function commondata(){
		$uimode = self::$uimode;
		$prefix = self::$prefix;
		$_sdata = self::getSiteData();
		$_uid = null;
		$cur_user = null;
		$allpermissions = self::getpermissions();
		$mypermissions = [];

		if(Auth::check()){
			$cur_user = Auth::user();
			// $_uid = self::encryptuid($cur_user->id);
		}

		return [
			'uimode' => $uimode,
			'prefix' => $prefix,
			'sitedata' => $_sdata,
			'uid' => $_uid,
			'cur_user' => $cur_user,
			'permissions' => $allpermissions,
			'mypermissions' => $mypermissions,
		];
	}

	public static function mekuid(){
		if(Auth::check()){
			$uid = Auth::user()->id;
			return self::encryptuid($uid);
		} else {
			return null;
		}
	}

	public static function encryptuid($uid){
		return Crypt::encrypt($uid);
	}

	public static function decryptuid($uid,&$error = null){
		try {
			$decrypted = Crypt::decrypt($uid);
			return $decrypted;
		} catch (DecryptException $e) {
			$error = $e;
			return null;
		}
	}

	public static function showJSON($what){
		$topass = [
			'thedata' => $what,
		];
		dd($topass);
	}

	public function curlGet(string $url){
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL				=> $url,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 30,
			CURLOPT_USERAGENT		=> 'Laravel App',
			CURLOPT_HTTPHEADER		=> ['Accept: application/json'],
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error    = curl_error($ch);

		curl_close($ch);

		if ($error) {
			throw new Exception("cURL error: $error");
		}

		return [
			'status' => $httpCode,
			'rawresponse' => $response,
			'body'   => json_decode($response, true) ?: null,
		];
	}

	public function getUserCountry(Request $req){
		$ip = $req->ip(); // user’s IP
		$location = Location::get($ip);

		if($location){
			return $location->countryCode;
		} else {
			return 'KE';
		}
	}

	public function getCountryList(Request $req){
		$countries = FileopsController::saferead(storage_path('runtime/countries_list.json'),true,12);
		$san_countries = json_decode($countries,true);
		$finres = [];
		$res = false;
		$msg = "procedure was unable to begin";

		if(is_array($san_countries)){
			$res = true;
			$msg = count($san_countries)." countries found";
			$finres = $san_countries;
		}

		$sendme = [
			'success' => true,
			'result' => $res,
			'message' => $msg,
			'sendme' => $finres,
		];

		if(isset($req['called']) && $req['called'] == true){
			return $finres;
		} else {
			return response()->json($sendme);
		}
	}

	public function getRates(){
		$xchange_storage = storage_path('runtime/exchange_rates.json');
		$refresh = true;
		$rates = [];
		$msgs = ["attempting to read exchange rates storage"];

		if(is_file($xchange_storage)){
			$msgs[] = "found file, reading it";
			$readstuff = FileopsController::saferead($xchange_storage,true,12);
			$savedData = json_decode($readstuff,true);
			$lastsave = Carbon::parse($savedData['lastsave']);		// parses the date the saved data was well, saved
			$tooOld = $lastsave->lt(Carbon::now()->subDay());		// checks if its more than a day ago

			$refresh = $tooOld;
			$rates = $savedData['rates'];
		}

		if($refresh){
			$msgs[] = "refreshing info";
			$rawrates = self::curlGet(self::$exchage_rates_url);
			$r_body = $rawrates['body'];
			$rates = $r_body['rates'];

			$nowTime = Carbon::now()->toISOString();
			$tosave = [
				'lastsave' => $nowTime,
				'rates' => $rates,
			];
			$json = json_encode($tosave,JSON_PRETTY_PRINT);

			FileopsController::safewrite_txt($xchange_storage,$json,true,15);
		}

		return [$msgs,$rates];
	}

	public function getAllRates(Request $req){
		$vl = $req->all();
		$fetchid = self::$fetchId;
		$isapi = isset($vl[$fetchid]) ? $vl[$fetchid] == 'true' || $vl[$fetchid] == "yes" : false;
		$success = false;
		$res = false;
		$sendme = [];
		$incl = [];
		$msg = "procedure was unable to begin";

		try{
			$gotten = $this->getRates();
			$incl[] = $gotten[0];
			$sendme = $gotten[1];
			$msg = 'data was gotten';
		} catch (\Throwable $th){
			$msg = $th->getMessage()." at ".$th->getFile().": ".$th->getLine();
		}

		// return response()->json(['fetched' => $this->getRates()]);

		$thedata = [
			'input' => $vl,
			'success' => $success,
			'result' => $res,
			'sendme' => $sendme,
			'xtras' => $incl,
			'message' => $msg,
		];

		// echo json_encode($isapi);
		// exit();

		// for debug purposes
		// return self::showJSON($thedata);
		// dd($thedata);

		return response()->json($thedata);
	}

	public function getMyRate(Request $req){
		$vl = $req->all();
		$countryCode = isset($vl['country']) ? $vl['country'] : self::getUserCountry($req);
		$thecode = Str::upper($countryCode);

		$passeddata = [
			'country' => $thecode,
		];

		$fetchid = self::$fetchId;
		$isapi = isset($vl[$fetchid]) ? $vl[$fetchid] == 'true' || $vl[$fetchid] == "yes" : false;
		$success = false;
		$res = false;
		$sendme = [];
		$incl = [$thecode];
		$msg = "procedure was unable to begin";

		$req['called'] = true;
		$countries = self::getCountryList($req);
		$allrates = $this->getRates();
		$rates = $allrates[1];
		$con = $passeddata['country'];
		$isapi = true;

		if(is_array($countries)){
			$success = true;
			$thecurrency = "??";
			$thename = "??????";
			$kcur = 'KES';

			foreach ($countries as $key => $value) {
				if($value['short_code'] === $con){
					$thecurrency = $value['currency'];
					$thename = $key;
				}
				if($value['short_code'] === "KE"){
					$kcur = $value['currency'];
				}
			}

			$krate = isset($rates[$kcur]) ? $rates[$kcur] : 128.9;
			$rate = isset($rates[$thecurrency]) ? $rates[$thecurrency] : 0.999999;
			$sendme = [
				'currency' => $thecurrency,
				'code' => $con,
				'name' => $thename,
				'krate' => $krate,
				'rate' => $rate,
			];
			$msg = "currency data retrieved";

			// run some operations only make true when its successful
			$res = true;
		} else {
			$incl[] = json_encode($countries);
			$msg = "error getting the Operating countries";
		}

		$thedata = [
			'input' => $vl,
			'success' => $success,
			'result' => $res,
			'sendme' => $sendme,
			'xtras' => $incl,
			'message' => $msg,
		];

		// echo json_encode($isapi);
		// exit();

		// for debug purposes
		// return self::showJSON($thedata);
		// dd($thedata);

		if($req['called'] == true){
			return $sendme;
		}

		if($isapi){
			return response()->json($thedata);
		}else{
			return response()->json($thedata);
			return redirect('./')->with(['success' => 'invalid access method']);
		}
	}

	public function getRate(Request $req,$country,$called = false){
		$vl = $req->all();
		$countryCode = isset($vl['country']) ? $vl['country'] : self::getUserCountry($req);
		$thecode = Str::upper($countryCode);

		$fetchid = self::$fetchId;
		$isapi = isset($vl[$fetchid]) ? $vl[$fetchid] == 'true' || $vl[$fetchid] == "yes" : false;
		$success = false;
		$res = false;
		$sendme = [];
		$incl = [$thecode];
		$msg = "procedure was unable to begin";

		$req['called'] = true;
		$countries = self::getCountryList($req);
		$allrates = $this->getRates();
		$rates = $allrates[1];
		$con = $country;
		$isapi = true;

		if(is_array($countries)){
			$success = true;
			$thecurrency = "??";
			$thename = "??????";
			$kcur = 'KES';

			foreach ($countries as $key => $value) {
				if($value['short_code'] === $con){
					$thecurrency = $value['currency'];
					$thename = $key;
				}
				if($value['short_code'] === "KE"){
					$kcur = $value['currency'];
				}
			}

			$krate = isset($rates[$kcur]) ? $rates[$kcur] : 128.9;
			$rate = isset($rates[$thecurrency]) ? $rates[$thecurrency] : 0.999999;
			$sendme = [
				'currency' => $thecurrency,
				'code' => $con,
				'name' => $thename,
				'krate' => $krate,
				'rate' => $rate,
			];
			$msg = "currency data retrieved";

			// run some operations only make true when its successful
			$res = true;
		} else {
			$incl[] = json_encode($countries);
			$msg = "error getting the Operating countries";
		}

		$thedata = [
			'input' => $vl,
			'success' => $success,
			'result' => $res,
			'sendme' => $sendme,
			'xtras' => $incl,
			'message' => $msg,
		];

		// echo json_encode($isapi);
		// exit();

		// for debug purposes
		// return self::showJSON($thedata);
		// dd($thedata);

		if($called){
			return $sendme;
		}

		if($isapi){
			return response()->json($thedata);
		}else{
			return response()->json($thedata);
			return redirect('./')->with(['success' => 'invalid access method']);
		}
	}

	// site data
	public static function defaultSiteData(){
		$created = Carbon::now()->toDateTimeString();
		$lastupdate = Carbon::parse(self::$sitedata_last_update);

		// this is the default site data that is regenerated if the sitedata file is corrupted or deleted
		// holds website information that can be changed by the admin via myAdmin panel
		$data = [
			'date_created' => $created,
			'date_updated' => $lastupdate,
			'branch_name' => 'migori',
			'sitelink' => 'https://migorimain.jaymat.com/',
			'currency' => 'KSH',
			"country_code"  => "KE",
			"initials" => "MGI",
			"location" => "Migori, Kenya",
			"manager"  => "??",
			"task_prefix" => "JML",
			"developer_details" => [
				'email' => 'corygshava777+jaymat_dev_migori@gmail.com',
				"name" => 'Cory',
			],
			"owner_details" => [
				'email' => 'jaymattianlabs@gmail.com',
				'name' => 'JayMatt',
			]
		];

		return json_encode($data,JSON_PRETTY_PRINT);
	}
	public static function getSiteData($useDefault = false,$forceReload = false){
		if(self::$sitedata != null && $forceReload === false) {return self::$sitedata;}

		$fl = new FileopsController();
		$sitedatapath = storage_path('runtime/sitedata.json');
		$dft = self::defaultSiteData();
		$parseddft = json_decode($dft,true);
		$sitedata = $parseddft;
		$err = '';

		$updatesitedata = function($what) use($dft,$sitedatapath,$fl,$err){
			if($fl::safewrite_txt($sitedatapath,$dft,true,25,$err) == false){
				die("something went wrong while trying to access website data: <br>\n\n$err");
			}
		};

		if($useDefault === true) return $sitedata;

		if(!is_file($sitedatapath)){
			$fl::c_file($sitedatapath,$err);

			$updatesitedata('--');
		} else {
			$contents = $fl::saferead($sitedatapath,true,12);
			$sitedata = json_decode($contents,true);
		}

		$saved_update = Carbon::parse($sitedata['date_updated']);
		$default_update = Carbon::parse($parseddft['date_updated']);
		$isobsolete = $saved_update->isBefore($default_update);

		if($isobsolete){
			$updatesitedata('nnx');
		}

		$sitedata['readError'] = $err;
		self::$sitedata = $sitedata;

		return $sitedata;
	}
	// end of sitedata

	public static function getSupportedCountries_paymethod($meth){
		$sdata = self::getSiteData(false);
		$udata = $sdata['user_data'];
		$countries = isset($udata['supportedCountries']) ? $udata['supportedCountries'] : [];
		$supported = isset($countries[$meth]) ? $countries[$meth] : [];
		return $supported;
	}

	public static function getSC_payments($meth){
		return self::getSupportedCountries_paymethod($meth);
	}

	public static function getvisits(){
		$fname = date('my')."_visitlogs.json";
		$fpath = storage_path('runtime/visitslogs/'.$fname);
		$_fl = new FileopsController();

		if(file_exists($fpath)){
			$contents = $_fl::saferead($fpath);
			$visits = json_decode($contents,true);
			return $visits;
		} else {
			return [$fpath];
		}
	}

	// system roles subsys
	public static function sys_permissions(){
		return [
			"last_update" => Carbon::parse(self::$sys_permissions_last_update),
			"permissions" => [
				// VIEW_ and MANAGE_ -> ui menu permissions
					"VIEW_DASHBOARD" => 'view the admin dashboard',
					"VIEW_CONSOLE" => 'view the normal user dashboard',
					"MANAGE_USER_ROLES" => 'view the different user roles',
					"MANAGE_USERS" => 'view all registered users',
					"MANAGE_CLIENTS" => 'view all registered clients',
					"VIEW_CLIENT" => 'view all registered clients',
					"MANAGE_TASKS" => 'view all tasks',
					"ADD_TASK" => 'add a new task',
					"MANAGE_SAMPLES" => 'view all sample records',
					"MANAGE_PAYMENTS" => 'view all payment records',
					"MANAGE_REPORTS" => 'view all reports',
					"MANAGE_INVOICES" => 'view all invoices',
					"MANAGE_BIZDATA" => 'view business data interface',
					"MANAGE_EXPENSES" => 'view expenses interface',
					"MANAGE_CASHREPORTS" => 'view cash reports interface',
					"VERIFY_FILES" => 'view file verification interface',
					"MANAGE_DEBTORS" => 'view all business debtors',
				// add more permissions later
					"TOGGLE_USER_ACTIVATE" => "activate / deactivate user accounts",
					"CHANGE_PASSWORDS" => "change other users' passwords",
					"CHANGE_OWN_PASSWORD" => "change their own password",
			],
		];
	}

	public static function getpermissions($useDefault = false,$forceReload = false){
		if(self::$permissions !== null && $forceReload){
			return self::$permissions;
		}

		// get defaults
		$dft = self::sys_permissions();
		$saved_data = [];
		$fl = new FileopsController();
		$fpath = storage_path('runtime/all_permissions.json');
		$err = "";

		$updatepermissions = function() use ($dft,$fl,$err,$fpath){
			$pms = array_keys($dft['permissions']);

			if(!$fl::safewrite_txt($fpath,json_encode($dft,JSON_PRETTY_PRINT),true,12,$err)){
				die("there was an error writing permissions file: $err");
			}

			systemData::query()->updateOrCreate(
				['item_name' => 'system_permissions'],
				['item_data' => $pms]
			);

			userRoles::query()->updateOrCreate(
				['role_name' => "admin"],
				['role_permissions' => $pms]
			);
		};

		if(is_file($fpath)){
			$rawdata = $fl::saferead($fpath, true, 12, $err);
			$inter_data = $rawdata == '' && typeOf($rawdata) == 'string' ? [] : json_decode($rawdata,true);
			$saved_data = is_array($inter_data) ? $inter_data : [];
		} else {
			$fl::c_file($fpath,$err);
			$updatepermissions();
		}

		if(empty($saved_data)){
			$updatepermissions();
			$saved_data = $dft;
		} else{
			$dft_update = Carbon::parse($dft['last_update']);
			$saved_update = Carbon::parse($saved_data['last_update']);
			$shouldupdate = $saved_update->isBefore($dft_update);

			if($shouldupdate){
				$updatepermissions();
				$saved_data = $dft;
			}
		}

		self::$permissions = $dft;

		return $dft['permissions'];
	}

	public static function getmypermissions(){
		$permissions = [];

		if(self::ili()){
			$user = Auth::user();
			$urole = $user->myRoles;
			$permissions = $urole == null ? [] : $urole->role_permissions;
		}

		self::$mypermissions = $permissions;

		return $permissions;
	}
	public static function haspermission($perm){
		$outres = false;

		if(self::ili()){
			$perms = self::getmypermissions();
            $outres = in_array(strtoupper($perm),$perms);
		}

		return $outres;
	}

	// model based agnostics
	public function getme(Request $r) {
		$d = $r['_thedata'];
		$reader = 'feedback';
		$result = null;

		$result = json_encode($d);

		if(isset($d['key'],$d['val'],$d['modelname'])){
			$key = $d['key'];
			$val = $d['val'];
			$themodel = $d['modelname'];
			$mod = "App\\Models\\{$themodel}";

			$result = "we made it in the filter";
			// return $result;

			if(class_exists($mod)){
				$result = $mod::where($key,$val)->first();
				// return redirect()->back()->withErrors(['error' => 'yep its changed']);
			}
		}

		return $result;
	}

	public function addme(Request $r) {
		$d = $r['_thedata'];
		$reader = 'feedback';

		if(isset($d['modelname'], $d['reader'], $d['sendme'])){
			// return redirect()->back()->withErrors(['error' => 'i passed the condition']);
			$themodel = $d['modelname'];
			$reader = $d['reader'];
			$mod = "App\\Models\\{$themodel}";
			$sendme = $d['sendme'];

			if(class_exists($mod)){
				$mod::create($sendme);
				// return redirect()->back()->withErrors(['error' => 'yep its changed']);
			} else {
				return redirect()->back()->withErrors(['error' => "the model '$themodel' does not exist"]);
			}
		} else {
			// return redirect()->back()->withErrors(['error' => 'pass all the data first']);
			return redirect()->back()->withErrors(['error' => json_encode($d)]);
		}

		if(self::isadmin()){
			return $this->$reader($r);
		} else {
			return true;
		}
	}

	public function delme(Request $r){
		$d = $r['_thedata'];
		$reader = 'feedback';

		if(isset($d['key'],$d['val'],$d['modelname'],$d['reader'])){
			$key = $d['key'];
			$val = $d['val'];
			$themodel = $d['modelname'];
			$reader = $d['reader'];
			$mod = "App\\Models\\{$themodel}";

			if(class_exists($mod)){
				$mod::where($key,$val)->delete();
				// return redirect()->back()->withErrors(['error' => 'yep it deleted']);
			} else {
				return redirect()->back()->withErrors(['error' => "the model '$themodel' does not exist"]);
			}
		} else {
			return redirect()->back()->withErrors(['error' => 'pass all the data first']);
			// return redirect()->back()->withErrors(['error' => json_encode($d)]);
		}

		if(self::isadmin()){
			return $this->$reader($r);
		} else {
			return true;
		}
	}

	public function editme(Request $r) {
		$d = $r['_thedata'];
		$reader = 'feedback';

		if(isset($d['key'],$d['val'],$d['modelname'],$d['reader'],$d['sendme'])){
			$key = $d['key'];
			$val = $d['val'];
			$themodel = $d['modelname'];
			$reader = $d['reader'];
			$mod = "App\\Models\\{$themodel}";
			$sendme = $d['sendme'];

			if(class_exists($mod)){
				$mod::where($key,$val)->update($sendme);
				// return redirect()->back()->withErrors(['error' => 'yep its changed']);
			} else {
				return redirect()->back()->withErrors(['error' => "the model '$themodel' does not exist"]);
			}
		} else {
			// return redirect()->back()->withErrors(['error' => 'pass all the data first']);
			return redirect()->back()->withErrors(['error' => json_encode($d)]);
		}

		if(self::isadmin()){
			return $this->$reader($r);
		} else {
			return true;
		}
	}

    // misc utilities
	public function log_my_payload(Request $req){
		$res = $this->dftres;
		$alldata = $req->all();

		/*
		$passed = $req->validate([
			'recid' => ['required']
		]);
		// */


		if($this->ili()){
			$res['success'] = true;

			$reslt = self::log_payload($alldata,'debug_payload');
			$msg = "logging payload";

			$res['result'] = $reslt;
			$res['message'] = $msg;
			$res['sendme'] = true;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}
	public function log_my_payload_fyls(Request $req,$bypass = 'false'){
		$res = $this->dftres;
		$alldata = $req->all();

		/*
		$passed = $req->validate([
			'recid' => ['required']
		]);
		// */


		$go = $bypass == 'yes' || $bypass == 'true' ? true : false;
		if($this->ili() || $go){
			$res['success'] = true;

			$fyls = [];
			$curdate = now()->format('dmyhis');

			foreach($req->allFiles() as $key => $fyl){
				if($fyl->isValid()){
					$myname = pathinfo($fyl->getClientOriginalName(),PATHINFO_FILENAME);
					$ext = $fyl->getClientOriginalExtension();
					$rndr = $this->mekRandomString(4);
					$fname = "[{$curdate}]_{$rndr}_{$myname}.{$ext}";
					$fyls[] = [$key => $fname];
					$thepath = self::$payload_path;
					$fyl->move(storage_path("{$thepath}files/",$fname), $fname);
				}
			}
			$alldata['files'] = $fyls;

			$reslt = self::log_payload($alldata,'debug_payload_fyls');
			$msg = "logging payload + files";

			$res['result'] = $reslt;
			$res['message'] = $msg;
			$res['sendme'] = $alldata;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}
    public static function log_payload($entry = 'empty_purpose',$fname = null,$usedate = true){
		$fl = new FileopsController();
		$tdate = $usedate ? "[".date('y_m_d')."] " : '';
		$fname = $fname == null ? 'payloads' : $fname;
		$thepath = self::$payload_path;
		$fstore = "{$thepath}{$tdate}{$fname}.json";
		$fpath = storage_path($fstore);
		$contents = '[]';
		$curdata = [];

		// echo "File storage: ".$fstore."\nline: ".json_encode($entry)."\n";

		if(is_file($fpath)){
			$contents = $fl::saferead($fpath,true,12);
		} else {
			$fl::c_file($fpath);
		}

		$curdata = $contents == '' ? $curdata : json_decode($contents,true);
		$tstring = Carbon::now()->timestamp;

		// echo json_encode($curdata)."\n";

		if(isset($curdata[$tstring])){
			usleep(0.2 * 1000000);
			$tstring .= Carbon::now()->timestamp;
		}

		$curdata[$tstring] = $entry;

		$tosave = json_encode($curdata,JSON_PRETTY_PRINT);
		return $fl::safewrite_txt($fpath,$tosave,true,24);
	}

    public static function verifyPassword($pw,&$rl = null,$urec = null){
		$decrypted_pw = EncController::decryptme($pw,4,'cwaizy',19);
		$rl = $decrypted_pw;

		if($urec == null){
			return Hash::check($decrypted_pw,self::cur_user()->password);
		} else {
			return Hash::check($decrypted_pw,$urec->password);
		}
    }

	public static function getPreviousDay(string $range): array{
		if (self::isDate($range)) {
			return [
				"start" => Carbon::parse($range)->subDay()->startOfDay(),
				"end" => Carbon::parse($range)->subDay()->endOfDay(),
				"tym" => Carbon::parse($range),
			];
		}

		[$start, $end, $tym] = match($range) {
			'today' => [
				Carbon::today()->subDay()->startOfDay(),
				Carbon::today()->subDay()->endOfDay(),
				Carbon::today(),
			],
			'yesterday' => [
				Carbon::yesterday()->subDay()->startOfDay(),
				Carbon::yesterday()->subDay()->endOfDay(),
				Carbon::yesterday(),
			],
			'juzi' => [
				Carbon::today()->subDays(2)->subDay()->startOfDay(),
				Carbon::today()->subDays(2)->subDay()->endOfDay(),
				Carbon::today()->subDays(2),
			],
			'kabla_juzi' => [
				Carbon::today()->subDays(3)->subDay()->startOfDay(),
				Carbon::today()->subDays(3)->subDay()->endOfDay(),
				Carbon::today()->subDays(3),
			],
			'this_week' => [
				Carbon::now()->startOfWeek()->subDay()->startOfDay(),
				Carbon::now()->endOfWeek()->subDay()->endOfDay(),
				Carbon::now()->startOfWeek(),
			],
			'this_month' => [
				Carbon::now()->startOfMonth()->subDay()->startOfDay(),
				Carbon::now()->endOfMonth()->subDay()->endOfDay(),
				Carbon::now()->startOfMonth(),
			],
			'this_year' => [
				Carbon::now()->startOfYear()->subDay()->startOfDay(),
				Carbon::now()->endOfYear()->subDay()->endOfDay(),
				Carbon::now()->startOfYear(),
			],
			'last_week' => [
				Carbon::now()->subWeek()->startOfWeek()->subDay()->startOfDay(),
				Carbon::now()->subWeek()->endOfWeek()->subDay()->endOfDay(),
				Carbon::now()->subWeek()->startOfWeek(),
			],
			'last_month' => [
				Carbon::now()->subMonth()->startOfMonth()->subDay()->startOfDay(),
				Carbon::now()->subMonth()->endOfMonth()->subDay()->endOfDay(),
				Carbon::now()->subMonth()->startOfMonth(),
			],
			'last_year' => [
				Carbon::now()->subYear()->startOfYear()->subDay()->startOfDay(),
				Carbon::now()->subYear()->endOfYear()->subDay()->endOfDay(),
				Carbon::now()->subYear()->startOfYear(),
			],
			default => [
				Carbon::parse(self::$system_start_date)->startOfDay(), Carbon::today(), Carbon::today()],
		};

		if (!$start) {
			return ['error' => "Unknown range '{$range}'"];
		}

		return [
			'start' => $start->toDateTimeString(),
			'end'   => $end->toDateTimeString(),
			'tym'   => $tym->toDateTimeString(),
		];
	}
	public static function isDate(string $value): bool{
		if (!is_string($value)) {
			return false;
		}

		try {
			Carbon::parse($value);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	// Templates
	public function ReqTemplate(Request $req){
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

			$cmdt = self::commondata();
			$out = $cmdt['permissions'];
			$mdta = random_int(0,14);
			$reslt = $mdta >= 7;

			$res['result'] = $reslt;
			$res['sendme'] = $out;
			$res['message'] = $msg;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}
}
