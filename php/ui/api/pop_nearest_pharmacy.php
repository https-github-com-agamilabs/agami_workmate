<?php
$base_path = dirname(dirname(dirname(dirname(__FILE__))));
//include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once($base_path . "/php/db/Database.php");
$db = new Database();
$dbcon = $db->db_connect();
if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}

try{

    if(isset($_POST['postcode']) && strlen($_POST['postcode'])){
        $postcode=(int) $_POST['postcode'];
    }else{
        throw new \Exception("You must select a postcode!!!", 1);
    }

    $search_key = "";
    if (isset($_POST['search_key']) && strlen($_POST['search_key']) > 0) {
        $search_key = trim(strip_tags($_POST['search_key']));
    }
    $pageno = 1;
    if(isset($_POST['pageno']) && strlen($_POST['pageno'])){
        $pageno=(int) $_POST['pageno'];
    }

    $limit = 10;
    if(isset($_POST['limit']) && strlen($_POST['limit'])){
        $limit=(int) $_POST['limit'];
    }

    $result=get_nearest_pharmacy($dbcon, $postcode, $search_key, $pageno, $limit);
    $nearestPharmacyArray = array();
    if($result->num_rows>0){
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $nearestPharmacyArray[]=$row;
        }
        $response['error'] = false;
        $response['results'] = $nearestPharmacyArray;
        $response['pagination'] ['more'] = ($result->num_rows==$limit ?  true:false);
    }else{
        throw new Exception("No Data!", 1);
    }
}catch(\Exception $e){
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

// ecom_stores(storeno,storeid,title,owner_peopleno,street,postcode,country,loclat,loclon,validated,createdat,createdby)
function get_nearest_pharmacy($dbcon, $postcode, $search_key, $pageno, $limit)
{
    $start = ($pageno-1)*$limit;
    $search = '%' . $search_key . '%';

    $sql="SELECT
                storeno, title, street,s.postcode,country,loclat, loclon,
               loc.po, loc.ps, loc.districtname
            FROM ecom_stores as s
                LEFT JOIN
                    (SELECT pc.po, pc.ps, pc.postcode, d.districtname
                    FROM loc_postoffice as pc
                    INNER JOIN
                        loc_district as d
                    ON pc.districtno=d.districtno) as loc
                ON s.postcode=loc.postcode
            WHERE validated=1 AND CONCAT(title,' ',loc.po) LIKE ?
            ORDER BY ABS(s.postcode-?) ASC
            LIMIT ?,?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("siii", $search, $postcode, $start, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
