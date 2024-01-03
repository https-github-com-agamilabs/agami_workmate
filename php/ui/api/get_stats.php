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



try{

    $result = get_stats($dbcon);
    if($result->num_rows>0){
        $response['results']=$result->fetch_array(MYSQLI_ASSOC);
        $response['error'] = false;
    }else{
        $response['error'] = true;
        $response['message'] = "No stats!";
    }
}catch(\Exception $e){
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);
$dbcon->close();

//gen_peopleprimary(peopleno,peopleid,firstname,lastname,countrycode,contactno,dob,gender,email,createdatetime,faf_parentpeopleno)
function get_stats($dbcon)
{
    $sql = "SELECT
                (select count(peopleno) FROM gen_peopleprimary) as patient_qty,
                (select count(visitno) FROM drrx_patientvisit) as prescription_qty,
                (select count(doctno) FROM drrx_doctorinfo) as doctor_qty,
                (select count(providerno) FROM hom_serviceprovider) as caregiver_qty,
                (select count(callno) FROM hom_servicecall) as servicecall_qty,
                (select count(chamberno) FROM drrx_doctorschamber) as healthcenter_qty,
                (select count(storeno) FROM ecom_stores) as store_qty,
                (select count(productno) FROM drrx_products) as medicine_qty";

    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

