<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    //orgno, userno, moduleno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['foruserno']) && strlen($_POST['foruserno']) > 0) {
        $foruserno = (int)$_POST['foruserno'];
    } else {
        throw new \Exception("User must be selected!", 1);
    }

    $anos = update_userorg($dbcon, $orgno, $foruserno, $_POST);
    if ($anos > 0) {
        $response['error'] = false;
        $response['message'] = "Updated Successfully.";
    } else {
        throw new \Exception("Could not update!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,
//              dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)
function update_userorg($dbcon, $orgno, $userno, $data)
{
    $params = array();
    $values = array();
    $types = "";

    $allowed_columns = array(
        'uuid' => 's',
        'ucatno' => 'i',
        'supervisor' => 'i',
        'moduleno' => 'i',
        'designation' => 's',
        'hourlyrate' => 'd',
        'monthlysalary' => 'd',
        'permissionlevel' => 'i',
        'dailyworkinghour' => 'i',
        'timeflexibility' => 'i',
        'shiftno' => 'i',
        'starttime' => 's',
        'endtime' => 's',
        'timezone' => 's',
        'isactive' => 'i'
    );

    $columns = array_keys($allowed_columns);
    for ($index = 0; $index < count($columns); $index++) {
        $key = $columns[$index];
        $type = $allowed_columns[$key];

        if (isset($data[$key])) {

            if (strcasecmp($type, "i") === 0) {
                // int check
                $valueOfKey = (int) trim(strip_tags($data[$key]));
            } else if (strcasecmp($type, "d") === 0) {
                // double/float/decimal check
                $valueOfKey = (float) trim(strip_tags($data[$key]));
            } else {
                // string check: strip, trim, escape
                $valueOfKey = mysqli_real_escape_string($dbcon, trim(strip_tags($data[$key])));
            }

            $params[] = "`$key`=?";
            $values[] = $valueOfKey;
            $types .= $type;
        }
    }

    if (count($params) <= 0) {
        // found no allowed keys to update
        return -1;
    }

    $values[] = $orgno;
    $types .= "i";

    $values[] = $userno;
    $types .= "i";

    $queryParts = implode(", ", $params);

    $sql = "UPDATE com_userorg
            SET $queryParts
            WHERE orgno=? AND userno=?";

    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param($types, ...$values);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->execute();
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
