<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try {

    $catno = -1;
    if (isset($_POST['catno']) && strlen($_POST['catno']) > 0) {
        $catno = (int)$_POST['catno'];
    }

    $pageno = 1;
    if (isset($_POST['pageno']) && strlen($_POST['pageno']) > 0) {
        $pageno = (int)$_POST['pageno'];
    }

    $limit = 20;
    if (isset($_POST['limit']) && strlen($_POST['limit']) > 0) {
        $limit = (int)$_POST['limit'];
    }

    $gallery_array = array();
    if ($catno > 0) {
        $rs_gallery = get_categorized_gallery($dbcon, $catno, $pageno, $limit);
        if ($rs_gallery->num_rows > 0) {
            while ($grow = $rs_gallery->fetch_array(MYSQLI_ASSOC)) {
                $gallery_array[] = $grow;
            }
        } else {
            throw new \Exception("No data found!", 1);
        }
    } else {
        for ($i = 1; $i < 5; $i++) {
            $rs_gallery = get_categorized_gallery($dbcon, $i, $pageno, $limit / 4);
            if ($rs_gallery->num_rows > 0) {
                while ($grow = $rs_gallery->fetch_array(MYSQLI_ASSOC)) {
                    $gallery_array[] = $grow;
                }
            }
        }
    }

    if (count($gallery_array) > 0) {
        $response['error'] = false;
        $response['data'] = $gallery_array;
    } else {
        throw new \Exception("No data found!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//web_gallerycategory (gallerycatno, cattitle)
//web_imagegallery (imageno,image_title,externallink,imageurl,imageactualname,thumbnailimageurl,catno,displayorderno,display_status,entrydatetime)

function get_categorized_gallery($dbcon, $catno, $pageno, $limit)
{
    $startindex = ($pageno - 1) * $limit;
    $sql = "SELECT imageno,image_title,externallink,imageurl,imageactualname,
                    thumbnailimageurl,displayorderno,display_status,
                    catno,(SELECT cattitle FROM web_gallerycategory WHERE catno=ig.catno) as cattitle,
                    entrydatetime
            FROM web_imagegallery as ig
            WHERE catno=?
            ORDER BY imageno DESC
            LIMIT ?,?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $catno, $startindex, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
