<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    session_destroy();
} else {
}

header("location: index.php");
exit();
