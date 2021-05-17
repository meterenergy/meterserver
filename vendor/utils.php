<?php

function nums_from_date(int $length = 11)
{
	$r_init = time();
	$r_start = 1;
	$r_end = 9;
	if (strlen($r_init) < $length) {
		while (strlen(strval($r_start)) < ($length - strlen($r_init))) {
			$r_start = $r_start * 10;
		}
		while (strlen(strval($r_end)) < ($length - strlen($r_init))) {
			$r_end = intval(strval($r_end) . '9');
		}
		$r_final = $r_init . strval(mt_rand($r_start, $r_end));
	}

	else $r_final = $r_init;
		
	return $r_final;
}

?>