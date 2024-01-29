<?php
$base_path = dirname(dirname(dirname(__FILE__)));

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once($base_path . "/db/Database.php");
$db = new Database();
$dbcon = $db->db_connect();
if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}



try {
    //$contactno,$email
    if (isset($_POST['contactno']) && strlen($_POST['contactno']) > 0) {
        $contactno = trim(strip_tags($_POST['contactno']));
    } else {
        throw new Exception("Contact-no cannot be empty!!", 1);
    }

    $result = get_people_by_contactno($dbcon, $contactno);
    if ($result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $response['result'] = $row;
        $response['error'] = true;

        $userinfo = get_user($dbcon, $row['peopleno']);
        if ($userinfo->num_rows > 0) {
            $response['create_user'] = 0;
            $response['message'] = "You are already our user!";
        } else {
            $response['create_user'] = 1;
            $response['message'] = "This contact number is already registered!";
        }
    } else {
        $response['error'] = false;
        $response['message'] = "Good! You may proceed.";
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);
$dbcon->close();

//gen_peopleprimary(peopleno,peopleid,firstname,lastname,countrycode,contactno,dob,gender,email,createdatetime)
function get_people_by_contactno($dbcon, $contactno)
{
    $sql = "SELECT peopleno,peopleid,firstname,lastname,countrycode,contactno,dob,gender,email,bloodgroup,createdatetime
            FROM gen_peopleprimary
            WHERE faf_parentpeopleno IS NULL AND contactno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("s", $contactno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}


//hr_user (userno,peopleno,email,username,passphrase)
function get_user($dbcon, $peopleno)
{
    $sql = "SELECT userno
            FROM hr_user
            WHERE peopleno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $peopleno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
