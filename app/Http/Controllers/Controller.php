<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Location\Facades\Location;
use App\Http\Controllers\FileopsController;

abstract class Controller{
	public static $prefix = 'ui';
	public static $uimode = "dark";
	public static $_dir = "admin";
	public static $sitedata = null;

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

    public static function isloggedin(){
		return auth()->check();
	}

	public static function ili() {
		return self::isloggedin();
	}
	
	public static function commondata(){
		$uimode = self::$uimode;
		$prefix = self::$prefix;
		$_dir = self::$_dir;
		$_sdata = self::getSiteData();
		$_uid = null;
		$cur_user = null;

		if(auth()->check()){
			$cur_user = auth()->user();
			$_uid = self::encryptuid($cur_user->id);
		}

		return [
			'uimode' => $uimode,
			'prefix' => $prefix,
			'_dir' => $_dir,
			'sitedata' => $_sdata,
			'uid' => $_uid,
			'cur_user' => $cur_user,
		];
	}

	public static function mekuid(){
		if(auth()->check()){
			$uid = auth()->user()->id;
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
		} catch (Illuminate\Contracts\Encryption\DecryptException $e) {
			$error = $e;
			return null;
		}
	}

	public function curlGet(string $url){
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => 'Laravel App',
			CURLOPT_HTTPHEADER     => ['Accept: application/json'],
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

	public static function defaultSiteData(){
		$created = Carbon::now()->toDateTimeString();
		$lastupdate = Carbon::parse('2025-11-21 03:25:29');

		// this is the default site data that is regenerated if the sitedata file is corrupted or deleted
		// holds website information that can be changed by the admin via myAdmin panel
		$data = [
			'date_created' => $created,
			'date_updated' => $lastupdate,
			'user_data' => [
				'deliveryCharge' => 300,
				'valueCutoff' => 30000,
				'sitelink' => 'https://app.haoselkenya.com/_hypernote/_a/',
				'dev_sitelink' => 'http://localhost/lrvl_hypernote/_a/',
				'PendingOrderLifetime' => 12,
				'NewSubPromocode' => 'NEWSUBSCRIBER_Y1',
				'returnDeadline' => 12,
				'pre-orderDiscount' => 19,
				'failurePercent' => 40,
				'failureCap' => 40000,
				'orderCompletionPoints' => 700,
				'creditsConversion' => 0.9,
				'creditsCap' => 45,
				'Mpesa' => [
					'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
					'BusinessShortCode' => '174379',
					'TestPhoneNumber' => '254708374149',
					'CallbackRoute' => 'mpesa_callback',
				],
				'PaymentMethods' => [
					'Mpesa' => 'mpesa',
					'Stripe' => 'card',
				],
				'AvailablePaymentMethods' => [
					'Mpesa' => 'mpesa',
					'Stripe' => 'card',
				],
				'supportedCountries' => [
					'Mpesa' => ['KE'],
					'Stripe' => ['KE','UG','TZ'],
					'Delivery' => ['KE','UG','TZ'],
				]
			],
			"developer_details" => [
				'email' => 'corygshava777+houseofjrmdev@gmail.com',
				"name" => 'Cory',
			],
			"owner_details" => [
				'email' => 'justruthmartin@gmail.com',
				'name' => 'Madam Ruth',
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
}
