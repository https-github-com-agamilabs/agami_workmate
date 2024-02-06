<?php

$base_dir = dirname(__FILE__);
include_once($base_dir . "/php/utility/Utils.php");

$util = new Utils();

if ($util->is_session_started() === false) {
    session_start();
}

if (!isset($_SESSION['wm_userno'])) {
    header("Location: index.php");
    exit();
}

$moduleno = -1;
if (isset($_SESSION['wm_moduleno'])) {
    $moduleno = (int) $_SESSION['wm_moduleno'];
}

$userno = $_SESSION['wm_userno'];

?>