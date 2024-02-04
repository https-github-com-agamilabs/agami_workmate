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

    //orgno, userno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['username']) && strlen($_POST['username']) > 0) {
        $username = strip_tags($_POST['username']);
    } else {
        throw new \Exception("User must be selected!", 1);
    }

    $result = get_userno($dbcon, $username);
    if ($result->num_rows > 0) {
        $foruserno = $result->fetch_array(MYSQLI_ASSOC)['userno'];
    } else {
        throw new Exception('Invalid User!', 1);
    }

    // ==============

    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int)$_POST['purchaseno'];
    } else {
        throw new \Exception("Package must be selected!", 1);
    }

    $dbcon->begin_transaction();
    $anos = add_userorg($dbcon, $orgno, $userno, $_POST);
    if ($anos > 0) {
        $appliedno = insert_appliedpackage($dbcon, $purchaseno, $orgno, $username, $addedby);
        if ($appliedno > 0) {
            $response['error'] = false;
            $response['message'] = "Added Successfully.";
        } else {
            $dbcon->rollback();
            throw new Exception('User-module failed! Check your package and try again.', 1);
        }
    } else {
        $dbcon->rollback();
        throw new \Exception("Could not add!", 1);
    }

    $dbcon->commit();
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//hr_user (userno,username,firstname,lastname,email,countrycode,primarycontact,passphrase,authkey,userstatusno,ucreatedatetime,updatetime)
function get_userno($dbcon, $username)
{

    $sql = "SELECT userno
            FROM hr_user
            WHERE username=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,jobtitle,hourlyrate,monthlysalary,permissionlevel,
//              dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)
function add_userorg($dbcon, $orgno, $userno, $data)
{
    $params = array();
    $values = array();
    $types = "";
    $qs = array();

    $params[] = "orgno";
    $values[] = $orgno;
    $types .= "i";
    $qs[] = '?';

    $params[] = "userno";
    $values[] = $userno;
    $types .= "i";
    $qs[] = '?';

    $optional_columns = array(
        'uuid' => 's',
        'ucatno' => 'i',
        'supervisor' => 'i',
        'moduleno' => 'i',
        'jobtitle' => 's',
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

    $columns = array_keys($optional_columns);
    for ($index = 0; $index < count($columns); $index++) {
        $key = $columns[$index];
        $type = $optional_columns[$key];

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

            $params[] = "`$key`";
            $values[] = $valueOfKey;
            $qs[] = '?';

            $types .= $type;
        } else {
            return -1;
        }
    }

    $columns = array_keys($optional_columns);
    for ($index = 0; $index < count($columns); $index++) {
        $key = $columns[$index];
        $type = $optional_columns[$key];

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

            $params[] = "`$key`";
            $values[] = $valueOfKey;
            $qs[] = '?';

            $types .= $type;
        }
    }

    if (count($params) <= 0) {
        // found no allowed keys to update
        return -1;
    }

    $queryParts = implode(", ", $params);
    $qs = implode(", ", $qs);

    $sql = "INSERT INTO com_orgs ($queryParts)
            VALUES($qs)";

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
    $result = $stmt->insert_id;
    $stmt->close();
    // echo $result;
    return $result;
}


//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
function insert_appliedpackage($dbcon, $purchaseno, $orgno, $foruserno, $appliedby)
{
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_appliedpackage(purchaseno,item,orgno,assignedto, appliedat, appliedby)
            VALUES(?,'ORGUSER',?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iissi", $purchaseno, $orgno, $foruserno, $appliedat, $appliedby);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}
