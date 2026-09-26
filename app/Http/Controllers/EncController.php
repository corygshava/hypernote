<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncController extends Controller
{
	// Don't comment or delete, required by encryptor and decryptor
	private static function genCharsList() {
		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$chars2 = strtoupper($chars);
		$nums = '1234567890';
		$symbols = '!@#$%^&*()-+_{}[]';
		$float = '§₻₈₅₂₋∃∄₉₆₃₀≐≍≊≇≄≅≖≙≜≟≢≮≯≣⊚⊗⊖⊙⊜⊝⊡⊞⊢';

		return $chars . $chars2 . $nums . $symbols . $float;
	}

	private static function mekpieces($text, $chunks = 3, $fill = '') {
		$outtxt = [];
		$length = mb_strlen($text, 'UTF-8');

		for ($x = 0; $x < $length; $x += $chunks) {
			$outtxt[] = mb_substr($text, $x, $chunks, 'UTF-8');
		}

		// Join with fill (though original JS just joins with salt later)
		return $outtxt;
	}

	public static function encryptme($m, $offset = null, $salt = '', $chunks = 1) {
		// Default values
		$salt = $salt === null ? '' : $salt;
		$chunks = $chunks === null ? 1 : $chunks;

		// Salt the data using mekpieces
		$pieces = self::mekpieces($m, $chunks, '');
		$touse = implode($salt, $pieces);

		// Get character list
		$fintxt = self::genCharsList();
		$thelist = preg_split('//u', $fintxt, -1, PREG_SPLIT_NO_EMPTY);

		$outxt = '';
		$roffset = random_int(1, (int)(mb_strlen($fintxt, 'UTF-8') / 2));

		$offset = $offset === null || $offset == 0 ? $roffset : $offset;

		// Encrypt each character
		$chars = preg_split('//u', $touse, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($chars as $el) {
			$myid = array_search($el, $thelist);
			if ($myid === false || $myid === null) {
				$outxt .= $el; // if not found, keep original
				continue;
			}

			$newid = $myid + $offset;
			$validid = ($count = count($thelist) + $newid) % count($thelist);

			$outxt .= $myid > 0 ? $thelist[$validid] : $el;
		}

		return $outxt;
	}

	public static function decryptme($m, $offset = null, $salt = '') {
		$thechars = self::genCharsList();
		$thelist = preg_split('//u', $thechars, -1, PREG_SPLIT_NO_EMPTY);
		$outtxt = '';

		$roffset = random_int(1, (int)(mb_strlen($thechars, 'UTF-8') / 2));
		$offset = $offset === null || $offset == 0 ? $roffset : $offset;

		// Decrypt each character
		$chars = preg_split('//u', $m, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($chars as $el) {
			$mid = array_search($el, $thelist);
			if ($mid === false || $mid === null) {
				$outtxt .= $el;
				continue;
			}

			$newid = $mid - $offset;
			$useid = (count($thelist) + $newid) % count($thelist);

			$outtxt .= $mid > 0 ? $thelist[$useid] : $el;
		}

		// Remove salt (reverse of salting)
		$salt = $salt === null ? '' : $salt;
		if (!empty($salt)) {
			$outtxt = str_replace($salt, '', $outtxt);
		}

		return $outtxt;
	}
}
