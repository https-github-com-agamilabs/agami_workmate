<?php

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION['wm_userno'])) {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$response = array();
		$response['error'] = true;
		$response['message'] = "Session timeout exceeded. Please login again.";
		echo json_encode($response);
		exit();
	}

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		header("Location: /");
		exit();
	}

	exit();
}

if (isset($_SESSION['wm_ucatno'])) {
	$ucatno = $_SESSION['wm_ucatno'];
}

$userno = $_SESSION['wm_userno'];

?>