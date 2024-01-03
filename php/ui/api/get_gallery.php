<?php
$base_path = dirname(dirname(dirname(__FILE__)));

$response = array();
if ($_SERVER['REQUEST_METHOD']!='POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}


try{
    require_once($base_path."/db/Database.php");
    $db = new Database();
    $dbcon=$db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    $pageno=1;
    if (isset($_POST['pageno']) && strlen($_POST['pageno'])>0){
        $pageno = (int)$_POST['pageno'];
    }

    $limit=10;
    if (isset($_POST['limit']) && strlen($_POST['limit'])>0){
        $limit = (int)$_POST['limit'];
    }


    $gallery_array=array();
    $rs_gallery=get_gallery($dbcon,$pageno,$limit);
    if($rs_gallery->num_rows>0){
        while ($grow=$rs_gallery->fetch_array(MYSQLI_ASSOC)) {
            $gallery_array[] = $grow;
        }
    }

    if(count($gallery_array)<=0){
        throw new \Exception("No Data Found!", 1);
    }
    $response['error'] = false;
    $response['data']=$gallery_array;

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//web_imagegallery (imageno,image_title,externallink,imageurl,imageactualname,thumbnailimageurl,catno,displayorderno,display_status,entrydatetime)

function get_gallery($dbcon,$pageno,$limit){
    $startindex=($pageno-1)*$limit;
    $sql = "SELECT imageno,image_title,externallink,imageurl,imageactualname,catno,
                    thumbnailimageurl,displayorderno,display_status,entrydatetime
            FROM web_imagegallery as cd
            ORDER BY imageno DESC
            LIMIT ?,?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii",$startindex,$limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

?>
