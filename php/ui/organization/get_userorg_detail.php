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
    $result = get_userorgs($dbcon, $orgno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['results'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No User at the Organization Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,jobtitle,hourlyrate,monthlysalary,permissionlevel,dailyworkinghour,timeflexibility,shiftno,starttime,endtime,isactive)
//hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,authkey,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive,userstatusno)
//com_modules(moduleno,moduletitle)
//com_shiftsettings(shiftno,shifttitle,starttime,endtime)
function get_userorgs($dbcon, $orgno)
{
    $sql = "SELECT uono,uo.orgno,
                    uo.userno,uo.uuid,
                    uo.ucatno,(SELECT ucattitle FROM hr_usercat WHERE ucatno=uo.ucatno) as ucattitle,
                    uo.supervisor,(SELECT CONCAT(firstname,' ', lastname) FROM hr_user s WHERE s.userno=uo.supervisor) as supervisor_name,
                    uo.moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=uo.moduleno) as moduletitle,
                    uo.jobtitle,uo.hourlyrate,uo.monthlysalary,uo.permissionlevel,uo.dailyworkinghour,
                    uo.timeflexibility,
                    uo.shiftno,(SELECT shifttitle FROM com_shiftsettings WHERE shiftno=uo.shiftno) as shifttitle,
                    uo.timezone,
                    uo.starttime,uo.endtime,uo.isactive,
                    u.username,u.firstname,u.lastname,u.email,u.primarycontact,u.userstatusno
            FROM com_userorg AS uo
                INNER JOIN hr_user as u ON u.userno=uo.userno
            WHERE uo.orgno=?
            ORDER BY uo.isactive DESC,u.firstname DESC";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}
?>
