<?php

$base_dir = dirname(__FILE__);
include_once($base_dir . "/php/utility/Utils.php");

$util = new Utils();

if ($util->is_session_started() === false) {
    session_start();
}

if (!isset($_SESSION['userno'])) {
    header("Location: login.php");
    exit();
}

$moduleno = -1;
if (isset($_SESSION['moduleno'])) {
    $moduleno = (int) $_SESSION['moduleno'];
}

$userno = $_SESSION['userno'];

?>