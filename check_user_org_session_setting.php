<?php

$base_dir = dirname(__FILE__);
include_once($base_dir . "/php/utility/Utils.php");

$util = new Utils();

if ($util->is_session_started() === false) {
    session_start();
}

if (!isset($_SESSION['cogo_userno'])) {
    header("Location: login.php");
    exit();
}

$moduleno = -1;
if (isset($_SESSION['cogo_moduleno'])) {
    $moduleno = (int) $_SESSION['cogo_moduleno'];
}

$userno = $_SESSION['cogo_userno'];

?>