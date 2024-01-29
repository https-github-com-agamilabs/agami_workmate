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
    //$imageno
    $imageno = -1;
    if (isset($_POST['imageno']) && strlen($_POST['imageno']) > 0) {
        $imageno = (int)$_POST['imageno'];
    }

    //$image_title,$externallink,$imageurl,$imageactualname,$thumbnailimageurl,
    $image_title = NULL;
    if (isset($_POST['image_title']) && strlen($_POST['image_title']) > 0) {
        $image_title = trim(strip_tags($_POST['image_title']));
    }

    $externallink = NULL;
    if (isset($_POST['externallink']) && strlen($_POST['externallink']) > 0) {
        $externallink = trim(strip_tags($_POST['externallink']));
    }

    if (isset($_POST['imageurl']) && strlen($_POST['imageurl']) > 0) {
        $imageurl = trim(strip_tags($_POST['imageurl']));
    } else {
        throw new \Exception("Media-file cannot be empty!", 1);
    }

    $imageactualname = NULL;
    if (isset($_POST['imageactualname']) && strlen($_POST['imageactualname']) > 0) {
        $imageactualname = trim(strip_tags($_POST['imageactualname']));
    }

    $thumbnailimageurl = NULL;
    if (isset($_POST['thumbnailimageurl']) && strlen($_POST['thumbnailimageurl']) > 0) {
        $thumbnailimageurl = trim(strip_tags($_POST['thumbnailimageurl']));
    }

    //$catno,$displayorderno,$display_status
    if (isset($_POST['catno']) && strlen($_POST['catno']) > 0) {
        $catno = (int) $_POST['catno'];
        if ($catno <= 0) {
            $catno = 2; //Photo
        }
    } else {
        $catno = 2; //Photo
    }

    $displayorderno = 99;
    if (isset($_POST['displayorderno']) && strlen($_POST['displayorderno']) > 0) {
        $displayorderno = (int) $_POST['displayorderno'];
    }

    $display_status = 1;
    if (isset($_POST['display_status']) && strlen($_POST['display_status']) > 0) {
        $display_status = (int) $_POST['display_status'];
    }

    if ($imageno > 0) {
        $unos = update_entry(
            $dbcon,
            $image_title,
            $externallink,
            $imageurl,
            $imageactualname,
            $thumbnailimageurl,
            $displayorderno,
            $display_status,
            $imageno
        );
        if ($unos > 0) {
            $response['error'] = false;
            $response['message'] = "Successfully Modified.";
        } else {
            throw new \Exception("Could not modify! Check data and try again.", 1);
        }
    } else {
        $newpkno = add_entry(
            $dbcon,
            $image_title,
            $externallink,
            $imageurl,
            $imageactualname,
            $thumbnailimageurl,
            $catno,
            $displayorderno,
            $display_status
        );
        if ($newpkno > 0) {
            $response['error'] = false;
            $response['message'] = "Successfully Added.";
        } else {
            throw new \Exception("Could not add! Check data and try again.", 1);
        }
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//web_imagegallery (imageno,image_title,externallink,imageurl,imageactualname,thumbnailimageurl,
//                    catno,displayorderno,display_status,entrydatetime)
function add_entry(
    $dbcon,
    $image_title,
    $externallink,
    $imageurl,
    $imageactualname,
    $thumbnailimageurl,
    $catno,
    $displayorderno,
    $display_status
) {

    date_default_timezone_set("Asia/Dhaka");
    $entrydatetime = date("Y-m-d H:i:s");
    $sql = "INSERT INTO web_imagegallery(image_title,externallink,imageurl,imageactualname,thumbnailimageurl,
                                      catno,displayorderno,display_status,entrydatetime)
            VALUES(?,?,?,?,?,?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param(
        "sssssiiis",
        $image_title,
        $externallink,
        $imageurl,
        $imageactualname,
        $thumbnailimageurl,
        $catno,
        $displayorderno,
        $display_status,
        $entrydatetime
    );
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();

    return $result;
}

function update_entry(
    $dbcon,
    $image_title,
    $externallink,
    $imageurl,
    $imageactualname,
    $thumbnailimageurl,
    $displayorderno,
    $display_status,
    $imageno
) {
    $sql = "UPDATE web_imagegallery
            SET image_title=?,externallink=?,imageurl=?,imageactualname=?,thumbnailimageurl=?,
                            displayorderno=?,display_status=?
            WHERE imageno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param(
        "sssssiii",
        $image_title,
        $externallink,
        $imageurl,
        $imageactualname,
        $thumbnailimageurl,
        $displayorderno,
        $display_status,
        $imageno
    );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
