<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";
?>
<?php
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

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("You must select an organization!", 1);
    }
    $result = get_userorgmodules($dbcon, $orgno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['results'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No User Module Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userorgmodules(orgno,userno,moduleno,verified)
//com_modules(moduleno,moduletitle)
function get_userorgmodules($dbcon, $orgno)
{
    $sql = "SELECT um.orgno,
                    um.userno,u.username,u.firstname,u.lastname,
                    moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=um.moduleno) as moduletitle,
                    verified
            FROM com_userorgmodules AS um
                INNER JOIN hr_user as u ON u.userno=um.userno
            WHERE um.orgno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}
?>
