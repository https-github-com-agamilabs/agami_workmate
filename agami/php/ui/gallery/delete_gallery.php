<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD']!='POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try{
    if (isset($_POST['imageno']) && strlen($_POST['imageno'])>0){
        $imageno = (int)$_POST['imageno'];
    }else{
        throw new \Exception("You must select an image!", 1);
    }

    $dnos = delete_entry($dbcon,$imageno);
    if($dnos>0)
    {
        $response['error'] = false;
        $response['message'] = "Successfully deleted.";
    }else {
        throw new \Exception("Could not delete! Please try again.",1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();

//web_imagegallery (imageno,image_title,externallink,imageurl,imageactualname,thumbnailimageurl,catno,displayorderno,display_status,entrydatetime)
function delete_entry($dbcon,$imageno)
{
    $sql = "DELETE
            FROM web_imagegallery
            WHERE imageno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $imageno);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();

        return $result;
}
 ?>
