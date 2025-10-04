<?php
	function showwords($str,$words=20){
		$str = $str ?? "This is a long string that has way more than twenty words just for the sake of showing you how to cut it properly without breaking words apart like substr would do.";
		$items = explode(' ', $str); // split into array of words

		if(count($items) > $words){
			$thechars = array_slice($items, 0, $words); // take first 20
			$result = implode(' ', $thechars)." ...";
		} else {
			$result = $str;
		}

		return $result;
	}

	function indicateStatus($state = 'public'){
		if($state === "private"){
			return "<i class=\"fa fa-lock\"></i>";
		} else {
			return '';
		}
	}
?>