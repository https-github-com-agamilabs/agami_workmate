<?php
$base_path = dirname(dirname(dirname(__FILE__)));
//include_once($base_path."/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {
    require_once($base_path."/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    $search_key = "";
    if (isset($_POST['search_key'])) {
        $search_key = trim(strip_tags($_POST['search_key']));
        //$filter['search_key'] = $search_key;
    }

    $pageno = 1;
    if (isset($_POST['pageno'])) {
        $pageno = (int) $_POST['pageno'];
        $pageno = max($pageno, 1);
    }

    $districtno = -1;
    if (isset($_POST['districtno'])) {
        $districtno = (int) $_POST['districtno'];
    }

    $limit = 3000;
    if (isset($_POST['limit'])) {
        $limit = (int) $_POST['limit'];
        $limit = max($limit, 10);
    }

    $result = get_postcodes_list($dbcon, $pageno, $limit, $districtno, $search_key);

    $meta_array = array();
    $response['pagination'] = array();

    if ($result->num_rows>0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
            // $meta_array[] = $row;
        }

        $response['results'] = $meta_array;
        $response['size'] = count($meta_array);

        if(count($meta_array)>=$limit){
            $response['pagination']['more'] = true;
        }else{
            $response['pagination']['more'] = false;
        }
    } else {
        $response['error'] = true;
        $response['message'] = "No Postcode info found!";
        $response['pagination']['more'] = false;
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

function get_postcodes_list($dbcon, $pageno, $limit, $districtno, $search_key){
    $search = '%'.$search_key.'%';
    $startindex=($pageno - 1) * $limit;

    $filter="1 ";
    if($districtno>0){
        $filter="districtno=$districtno ";
    }

    $sql = "SELECT postcode, po, ps,
                districtno, iscity,
                (SELECT districtname FROM loc_district WHERE districtno=a.districtno) as districtname
            FROM loc_postoffice as a
            WHERE $filter AND CONCAT(postcode,' ',po,' ',ps) LIKE ?
            Limit ?,?";

    $stmt = $dbcon->prepare($sql);
    if(!$stmt){
        echo $dbcon->error;
    }
    $stmt->bind_param("sii", $search,$startindex,$limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
