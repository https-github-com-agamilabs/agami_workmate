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
    //$itemno,$orgno,$purchaseno,$appliedby,$duration
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

    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int) $_POST['purchaseno'];
    }else{
        throw new Exception("Scheme must be selected", 1);
    }

    if (isset($_POST['duration']) && strlen($_POST['duration']) > 0) {
        $duration = (int) $_POST['duration'];
    }else{
        throw new Exception("Duration cannot be empty!", 1);
    }

    $inos = apply_package($dbcon, $itemno,$orgno,$purchaseno,$userno,$duration);

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


//pack_appliedpackage(appliedno,purchaseno,orgno,starttime, duration,appliedat, appliedby)
function apply_package($dbcon,$orgno,$foruserno,$purchaseno,$appliedby,$duration)
{
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");
    // $validuntil=date('Y-m-d H:i:s', strtotime($appliedat. ' + '.($duration-1).' days'));
    // $validuntil = strtotime("tomorrow", $validuntil) - 1;

    $lastvaliduntil=get_last_validuntil($dbcon, $orgno);
    $lastvaliduntil=isset($lastvaliduntil)?($lastvaliduntil>$appliedat?$lastvaliduntil:$appliedat):$appliedat;

    $sql = "INSERT INTO pack_appliedpackage(purchaseno,orgno,starttime, duration,appliedat, appliedby)
                SELECT po.purchaseno, ? as orgno, ? as starttime, o.duration, ? as appliedat,? as appliedby
                FROM pack_purchaseoffer as po
                    INNER JOIN pack_offer as o ON po.offerno=o.offerno
                WHERE po.purchaseno=? AND po.foruserno=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("issiii", $orgno,$lastvaliduntil, $appliedat,$appliedby,$purchaseno,$foruserno);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return false;
    }
}

function get_last_validuntil($dbcon, $orgno)
{
    $sql="SELECT DATE(DATE_ADD(starttime, INTERVAL duration+1 DAY)) as lastdate
            FROM pack_appliedpackage
            WHERE orgno=?
            ORDER BY DATE(DATE_ADD(starttime, INTERVAL duration DAY)) DESC
            LIMIT 1";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $lastvaliduntil=NULL;
    $stmt->bind_param("i", $orgno);
    if ($stmt->execute()) {
        $rs_validuntil = $stmt->result();
        if ($rs_validuntil->num_rows > 0) {
            $lastvaliduntil = $rs_validuntil->fetch_array(MYSQLI_ASSOC)['lastdate'];
        }
        $stmt->close();
    } 
    return $lastvaliduntil;
}
?>
