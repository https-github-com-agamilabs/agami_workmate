<?php
$basePath = dirname(dirname(dirname(__FILE__)));
include_once($basePath . "/configmanager/org_configuration.php");

if (!defined("DB_USER")) {
    include_once $basePath . '/php/db/config.php';
}

$orglogo = isset($_SESSION['org_picurl']) ? $_SESSION['org_picurl'] : $publicAccessUrl . $response["orglogourl"];

?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $response['orgname']; ?></title>
<link rel="shortcut icon" type="image/png" sizes="16x16" href="<?= $orglogo; ?>">

<meta name="description" content="<?= $response['orgname']; ?>">
<meta name="keywords" content="<?= $response['orgname']; ?>">

<?php
$debug = false;
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "localhost";
$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$debughost = array('localhost', '192.168.1');

for ($i = 0; $i < count($debughost); $i++) {
    $adebughost = $debughost[$i];
    if (strpos($host, $adebughost) === 0) {
        $debug = true;
    }
}

if ($debug) {
    include_once dirname(__FILE__) . "/dependency_style_script_offline.php";
} else {
    include_once dirname(__FILE__) . "/dependency_style_script_online.php";
}

?>

<link href="<?= $publicAccessUrl; ?>css/main.css" rel="stylesheet">
<!-- <script type="text/javascript" src="../js/main.js"></script> -->
<!-- include if main.js is not included -->
<script src="https://cdn.jsdelivr.net/npm/metismenu@3.0.7/dist/metisMenu.min.js" integrity="sha256-CXoFWtETCSSvEQ9gUNr0+y97x8d6Bjkp9mZwvBfuFqI=" crossorigin="anonymous"></script>
<script src="<?= $publicAccessUrl; ?>shared/layout/minimized.main.js"></script>

<link rel="stylesheet" href="<?= $publicAccessUrl; ?>shared/layout/custom-style.css">
<script src="<?= $publicAccessUrl; ?>shared/layout/custom-javascript.js"></script>
<script>
    const publicAccessUrl = `<?= $publicAccessUrl ?>`;
</script>