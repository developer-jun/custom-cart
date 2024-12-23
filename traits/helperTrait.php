<?php
namespace Traits;

trait HelperTrait {	
	public function format($value, $currency = '$', $encoding = true) {
		if (!$encoding) {
			return 

			$curr = (ord($currency) == "128") ? "&#8364;" : htmlentities($currency);
		} else {
			$curr = $currency;
		}
		$formatted = $curr." ".number_format($value, 2, ",", ".");
		return $formatted;
	}
	
	public function prepare_string_value($value) {
		$new_value = (!get_magic_quotes_gpc()) ? addslashes($value) : $value;
		$new_value = ($value != "") ? "'".trim($value)."'" : "''";
		return $new_value;
	}
}