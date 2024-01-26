<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    }else{
        throw new Exception("Organization must be selected!!", 1);
    }

    if (isset($_POST['setid']) && strlen($_POST['setid']) > 0) {
        $setid = trim(strip_tags($_POST['setid']));
    }else{
        throw new Exception("Setting must be selected!!", 1);
    }

    $result = delete_an_orgsettings($dbcon,$orgno,$setid);

    if ($result>0) {
        $response['error'] = false;
        $response['message'] = 'Removed successfully!';
    } else {
        $response['error'] = true;
        $response['message'] = 'Data Error! Check the data.';
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//acc_orgsettings(orgno,setid, setlabel, fileurl)
function delete_an_orgsettings($dbcon,$orgno,$setid)
  {
    $sql = "DELETE
            FROM acc_orgsettings
            WHERE orgno=? AND setid=?";

    $stmt=$dbcon->prepare($sql);
	$stmt->bind_param("is",$orgno,$setid);
	$stmt->execute();
	$result = $stmt->affected_rows;
    $stmt->close();

    return $result;
  }
?>
