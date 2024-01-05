<?php


$base_path = dirname(dirname(__FILE__));
require_once($base_path . "/php/db/Database.php");

function getProjectConfig()
{
    $config = array(
        'site_root' => $_SERVER['DOCUMENT_ROOT'],
        'publicAccessUrl' => $GLOBALS['publicAccessUrl'],
        'projectPath' => $GLOBALS['projectPath'],
        'debug' => $GLOBALS['debug']
    );

    if ($GLOBALS['debug']) {
        $config['files_root'] = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['projectPath'] . "assets" . DIRECTORY_SEPARATOR;
    } else {
        $config['files_root'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR;
    }

    return $config;
}

function findFileName($fileurl)
{
    $filename_arr = explode(DIRECTORY_SEPARATOR, normalisePath($fileurl));

    return $filename_arr[count($filename_arr) - 1];
}

function findFileBaseUrl($fileurl)
{
    $processingFileurl = normalisePath($fileurl);
    $pc = getProjectConfig();

    $host = $pc['publicAccessUrl'];
    $hostbase = $pc['site_root'];
    $debug = $pc['debug'];

    $isSameHost = matchSameHost($processingFileurl, $host);

    if (!$isSameHost) {
        // echo "Not same host";
        return false;
    }

    // var_dump(explode('://', $processingFileurl));

    $filebase_arr = explode(DIRECTORY_SEPARATOR, explode('://', $processingFileurl)[1]);

    $filebase_extract_index = $debug ? 1 : 0;

    //var_dump($filebase_arr);
    array_splice($filebase_arr, 0, $filebase_extract_index);
    // var_dump($filebase_arr);
    $fileBaseUrl = $hostbase . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $filebase_arr);

    return $fileBaseUrl;
}


function matchSameHost($url1, $url2)
{
    $purl1 = parse_url($url1);
    $purl2 = parse_url($url2);

    return $purl1['host'] == $purl2['host'];
}

//
function generatePublicUrl($fileBaseUrl)
{
    $fileBaseUrl = normalisePath($fileBaseUrl);


    $pc = getProjectConfig();

    $host = $pc['publicAccessUrl'];
    $hostbase = $pc['site_root'];
    $projectPath = $pc['projectPath'];
    $debug = $pc['debug'];

    $index = strpos($fileBaseUrl, $hostbase);
    if ($index === false) {
        //echo "Cannot generate public url for ".$fileBaseUrl;
        return false;
    }

    if ($debug) {
        $filepublicurl = str_replace($hostbase . $projectPath, $host, $fileBaseUrl);
    } else {
        $filepublicurl = str_replace($hostbase, $host, $fileBaseUrl);
    }

    return normalisePath($filepublicurl);
}

function normalisePath($path)
{
    //$path = implode(DIRECTORY_SEPARATOR, explode("\/", $path));
    $path = preg_replace("/\\\\{2,}/", "\\", $path);

    $path_arr = explode("://", $path); //

    $arr = array();

    if (count($path_arr) > 1) {
        $arr[0] = $path_arr[0]; // prefix
        $arr[1] = implode("/", explode('//', $path_arr[1])); // rest

        $path = implode('://', $arr);
    } else {
        $arr[0] = implode("/", explode('//', $path_arr[0])); // rest

        $path = implode('', $arr);
    }

    return $path;
}


function random_string($length)
{
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}
