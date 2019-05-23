<?php
define('MAX_LOGIN_ATTEMPTS', 5);

function auth_attempted() {
	if (!is_numeric(@$_SESSION['login_attempts']))
		$_SESSION['login_attempts'] = 0;

	if (++$_SESSION['login_attempts'] > MAX_LOGIN_ATTEMPTS) {
		$_SESSION['user_state'] = "timelocked";
		$_SESSION['timelock'] = time() + 3600; /* lockout for 1 hour */
	}
}

function generate_2fa() {
	$_SESSION['2fa_expiry'] = time() + 60; /* 1 minute expiry */
	srand(time());
	return sprintf("%06d", rand(0, 999999));
}

function is_valid_2fa($code) {
	return (time() < $_SESSION['2fa_expiry'] && hash_equals($code, $_SESSION['2fa_code']));
}

/* assume these two functions are imported from elsewhere and are secure */
function is_user_password_valid($user, $password) {}
function send_2fa_to_user($user, $code) {}

/*** MAIN SCRIPT ***/
switch (session_status()) {
	case PHP_SESSION_DISABLED:
		http_response_code(500);
		exit();

	case PHP_SESSION_ACTIVE:
		break;

	default:
		@session_start();
}

if (!isset($_SESSION['user_state']))
	$_SESSION['user_state'] = 'login';

switch ($_SESSION['user_state']) {
case 'login':
	if (isset($_POST['user'], $_POST['password'])) {
		if (is_user_password_valid($_POST['user'], $_POST['password'])) {
			$_SESSION['user'] = $_POST['user'];
			$_SESSION['user_state'] = '2fa'; /* password is valid, require two-factor auth */
			$_SESSION['2fa_code'] = generate_2fa();
			send_2fa_to_user($_SESSION['user'], $_SESSION['2fa_code']);
		} else {
			auth_attempted();
		}
	}
	break;

case '2fa':
	if (isset($_POST['2fa_code'])) {
		if (is_valid_2fa($_POST['2fa_code'])) {
			$_SESSION['user_state'] = "logged_in";
			unset($_SESSION['2fa_code']);
			unset($_SESSION['2fa_expiry']);
		} else {
			auth_attempted();
		}
	}
	break;

case 'timelocked':
	if (time() < $_SESSION['timelock']) {
		http_response_code(403);
		exit();
	} else { /* timelock has expired */
		unset($_SESSION['timelock']);
		$_SESSION['user_state'] == 'login';
		$_SESSION['login_attempts'] = 0;
	}
	break;
}

require("views/{$_SESSION['user_state']}.php");
?>
