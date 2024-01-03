<?php

date_default_timezone_set('Asia/Dhaka');

$debug = true;
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "localhost";
$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$debughost = array('localhost', '192.168');

for ($i = 0; $i < count($debughost); $i++) {
    // code...
    $adebughost = $debughost[$i];
    // echo $adebughost."<br/>";
    if (strpos($host, $adebughost) === 0) {
        $debug = true;
        break;
    } else {
        $debug = false;
    }
}

// echo $debug."CONFIG HOST: ".$host;

if ($debug) {
    $projectName = "agami_workmate";

    #MySQL Database name:
    define('DB_NAME', 'workmatedb');

    #MySQL Database User Name:
    define('DB_USER', 'root');

    #MySQL Database Password:
    define('DB_PASSWORD', '11135984');
    //define('DB_PASSWORD', '');

    #MySQL Hostname:
    // define('DB_HOST', '127.0.0.1:3306');
    define('DB_HOST', 'localhost');
    #Table Prefix:
    define('PREFIX', 'msg');

    #Session Timeout Time:
    define('SESSION_TIMEOUT', 360000);

    #Major version:
    define('VERSION', '1.0');

    // echo "New HOST: ".(strcasecmp($host, "localhost")==0?$host:($host.":3306"));

    $publicAccessUrl = $REQUEST_PROTOCOL . "://$host/" . $projectName . "/";
    $projectPath = DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR;
    $authUrl = $REQUEST_PROTOCOL . "://$host/" . $projectName . "/auth/?redirect=";
} elseif (strcasecmp($host, "workmate.agamilabs.com")===0) {
    $projectName = "workmate.agamilabs.com";

    #MySQL Database name:
    define('DB_NAME', 'workmatedb');
    #MySQL Database User Name:
    define('DB_USER', 'workmate_admn');
    #MySQL Database Password:
    define('DB_PASSWORD', ']H}gX{)XGnAf');
    #MySQL Hostname:
    define('DB_HOST', 'localhost');

    define('PREFIX', '');

    $publicAccessUrl = $REQUEST_PROTOCOL . "://$host/";
    $projectPath = DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR;
    $authUrl = $REQUEST_PROTOCOL . "://$host/auth/?redirect=";
} else {
    $err_msg =  "$host is not allowed in Production Mode. Please contact AGAMiLabs Ltd.";
    if ($_SERVER['REQUEST_METHOD']!='POST') {
        echo $err_msg;
    } else {
        $response = array();
        $response['error'] = true;
        $response['message'] = $err_msg;
    }

    exit();
}
