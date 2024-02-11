<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";


try {
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        if (!isset($orgno) || strlen($orgno) <= 0) {
            throw new Exception("Organization must be selected!!", 1);
        }
    }

    $rs_storyphase = get_storyphase($dbcon, $orgno);

    if ($rs_storyphase->num_rows > 0) {
        $meta_array = array();
        while ($row = $rs_storyphase->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }
        $response['error'] = false;
        $response['results'] = $meta_array;
    } else {
        throw new \Exception("No Data Found!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//asp_storyphase(storyphaseno, orgno, storyphasetitle, colorno)
//asp_color(colorno, colorcode, colortitle)
function get_storyphase($dbcon, $orgno)
{
    $sql = "SELECT sp.storyphaseno,sp.storyphasetitle, 
                sp.colorno,c.colorcode, c.colortitle
            FROM asp_storyphase as sp
                LEFT JOIN asp_color as c ON c.colorno=sp.colorno
            WHERE sp.orgno=?
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
