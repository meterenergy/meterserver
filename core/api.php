<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Content-Type: application/json; charset=UTF-8');

Route::get('/api', function() {
	echo urlencode('{');
	// function getRef() {
	// 	$r_init = time();
	// 	$r_start = 1;
	// 	$r_end = 9;
	// 	if (strlen($r_init) < 12) {
	// 		while (strlen(strval($r_start)) < (12 - strlen($r_init))) {
	// 			$r_start = $r_start * 10;
	// 		}
	// 		while (strlen(strval($r_end)) < (12 - strlen($r_init))) {
	// 			$r_end = intval(strval($r_end) . '9');
	// 		}
	// 		$r_final = $r_init . strval(mt_rand($r_start, $r_end));
	// 	}

	// 	else $r_final = $r_init;
		
	// 	return $r_final;
	// }
 //    echo getRef();
});

Route::post('/api/register', function() {
    echo json_encode(UserController::register());
});

Route::post('/api/login', function() {
    echo json_encode(UserController::login());
});

Route::post('/api/settings', function() {
	echo json_encode(Controller::settings());
});

Route::post('/api/user', function() {
	echo json_encode(UserController::user());
});

Route::post('/api/user/wallet', function() {
	echo json_encode(UserController::wallet());
});

Route::get('/api/discos', function() {
	echo json_encode(IRecharge::getdiscos());
});

Route::post('/api/meter/verify', function() {
    echo json_encode(UserController::verify_meter());
});

Route::post('/api/meter/purchase', function() {
    echo json_encode(UserController::vend_meter());
});

Route::post('/api/payment', function() {
	echo json_encode(UserController::payment());
});

Route::post('/api/payment/charge', function() {
	echo json_encode(UserController::charge_payment());
});

?>
