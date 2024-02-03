<?php

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION['cogo_userno'])) {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$response = array();
		$response['error'] = true;
		$response['message'] = "Session timeout exceeded. Please login again.";
		echo json_encode($response);
		exit();
	}

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		header("Location:index.php");
		exit();
	}

	exit();
} else {
	// if (!isset($_SESSION['cogo_ucatno'])) {
	// 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// 		$response = array();
	// 		$response['error'] = true;
	// 		$response['message'] = "Session timeout exceeded. Please login again.";
	// 		echo json_encode($response);
	// 		exit();
	// 	}

	// 	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	// 		header("Location:index.php");
	// 		exit();
	// 	}
	// 	exit();
	// }
}

$userno = $_SESSION['cogo_userno'];
$ucatno = $_SESSION['cogo_ucatno'];
$my_permissionlevel = $_SESSION['cogo_permissionlevel'];
?>