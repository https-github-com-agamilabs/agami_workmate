<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    //orgno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
        $result = update_organization($dbcon, $orgno, $_POST);
        if ($result > 0) {
            $response['error'] = false;
            $response['message'] = "Updated Successfully.";
        } else {
            throw new Exception("Could not update data!", 1);
        }
    } else {
        $dbcon->begin_transaction();
        $orgno = add_organization($dbcon, $userno, $_POST);
        if ($orgno > 0) {
            $result = insert_userorgs($dbcon, $orgno, $userno);
            if ($result <= 0) {
                $dbcon->rollback();
                throw new Exception("Could not grant ownership!", 1);
            }

            $init = insert_accountheads($dbcon, $orgno);
            if ($init <= 0) {
                $dbcon->rollback();
                throw new Exception("Could not initiate accounts!", 1);
            }

            if ($dbcon->commit()) {
                $response['error'] = false;
                $response['message'] = "Added Successfully.";
            } else {
                $dbcon->rollback();
                throw new Exception("Fatal Error! Could not save.", 1);
            }
        } else {
            throw new Exception("Could not add data!", 1);
        }
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy,
//        picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
function add_organization($dbcon, $addedby, $data)
{
    $params = array();
    $values = array();
    $types = "";
    $qs = array();

    $required_columns = array(
        'orgname' => 's',
        'orgtypeid' => 'i',
        'contactno' => 's',
    );

    $optional_columns = array(
        'street' => 's',
        'city' => 's',
        'state' => 's',
        'street' => 's',
        'country' => 's',
        'gpslat' => 'd',
        'gpslon' => 'd',
        'privacy' => 'i',
        'picurl' => 's',
        'orgnote' => 's',
        'weekend1' => 's',
        'weekend2' => 's',
        'starttime' => 's',
        'endtime' => 's'
    );

    $columns = array_keys($required_columns);
    for ($index = 0; $index < count($columns); $index++) {
        $key = $columns[$index];
        $type = $required_columns[$key];

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

    //$params[] = "addedby";
    //$values[] = $addedby;
    //$types .= "i";
    //$qs[] = '?';

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

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy,
//        picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
function update_organization($dbcon, $orgno, $data)
{
    $params = array();
    $values = array();
    $types = "";

    $allowed_columns = array(
        'orgname' => 's',
        'street' => 's',
        'city' => 's',
        'state' => 's',
        'street' => 's',
        'country' => 's',
        'gpslat' => 'd',
        'gpslon' => 'd',
        'orgtypeid' => 'i',
        'privacy' => 'i',
        'picurl' => 's',
        'contactno' => 's',
        'orgnote' => 's',
        'weekend1' => 's',
        'weekend2' => 's',
        'starttime' => 's',
        'endtime' => 's'
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

    $queryParts = implode(", ", $params);

    $sql = "UPDATE com_orgs
            SET $queryParts
            WHERE orgno=?";

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

//acc_userorgmodules(orgno,userno,moduleno,verified)
function insert_userorgs($dbcon, $orgno, $userno)
{
    $sql = "INSERT INTO acc_userorgmodules(orgno,userno,moduleno,verified)
            VALUES(?,?,1,1)";

    $stmt = $dbcon->prepare($sql);
    // var_dump($this->dbcon->error);

    if ($stmt) {
        $stmt->bind_param('ii', $orgno, $userno);
        if ($stmt->execute()) {
            $flag = $stmt->affected_rows;
            if ($flag > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function insert_accountheads($dbcon, $orgno)
{
    $sql = "INSERT INTO acc_orgaccounthead(orgno, accno, accname, acctypeno, levelno, vtype, sysacc) VALUES
            ($orgno, 10000, 'ASSETS', 1000,1,0,1),
            ($orgno, 20000, 'LIABILITIES', 2000,1,0,1),
            ($orgno, 30000, 'EXPENSES', 3000,1,0,1),
            ($orgno, 40000, 'REVENUES', 4000,1,0,1)";

    $stmt = $dbcon->prepare($sql);
    // var_dump($this->dbcon->error);

    if ($stmt) {
        if ($stmt->execute()) {
            $flag = $stmt->affected_rows;
            if ($flag > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}
