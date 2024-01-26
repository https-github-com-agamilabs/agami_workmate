<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
$response['error'] = false;
$response['message'] = '';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try{
    //$itemno,$orgno,$schemeno,$appliedby,$duration
    if (isset($_POST['itemno']) && strlen($_POST['itemno']) > 0) {
        $itemno = (int) $_POST['itemno'];
    }else{
        throw new Exception("Item must be selected", 1);
    }

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    }else{
        throw new Exception("Organization must be selected", 1);
    }

    if (isset($_POST['schemeno']) && strlen($_POST['schemeno']) > 0) {
        $schemeno = (int) $_POST['schemeno'];
    }else{
        throw new Exception("Scheme must be selected", 1);
    }

    if (isset($_POST['duration']) && strlen($_POST['duration']) > 0) {
        $duration = (int) $_POST['duration'];
    }else{
        throw new Exception("Duration cannot be empty!", 1);
    }

    $inos = apply_package($dbcon, $itemno,$orgno,$schemeno,$userno,$duration);

    if ($inos == false) {
        throw new Exception("Could not apply package!", 1);
    } else {
        $response['error'] = false;
        $response['message'] = 'Package applied successfully.';
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//pack_appliedpackage(itemno,orgno,schemeno,appliedat,appliedby,validuntil)
function apply_package($dbcon, $itemno,$orgno,$schemeno,$appliedby,$duration)
{
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");
    $validuntil=date('Y-m-d H:i:s', strtotime($appliedat. ' + '.($duration-1).' days'));
    $validuntil = strtotime("tomorrow", $validuntil) - 1;

    $sql = "INSERT INTO pack_appliedpackage(itemno,orgno,schemeno,appliedat,appliedby,validuntil)
            VALUES(?,?,?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iiisis", $itemno,$orgno,$schemeno,$appliedat,$appliedby,$validuntil);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return false;
    }
}
?>
