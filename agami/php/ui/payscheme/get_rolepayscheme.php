<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD']!='POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try{

    $userroleno = -1;
    if (isset($_POST['userroleno']) && strlen($_POST['userroleno'])>0){
        $userroleno = (int)$_POST['userroleno'];
    }

    $pageno=1;
    if (isset($_POST['pageno']) && strlen($_POST['pageno'])>0){
        $pageno = (int)$_POST['pageno'];
    }

    $limit=20;
    if (isset($_POST['limit']) && strlen($_POST['limit'])>0){
        $limit = (int)$_POST['limit'];
    }

    $rolepayscheme_array=array();
    if($userroleno>0){
        $rs_rolepayscheme=get_rolewise_rolepayscheme($dbcon,$userroleno,$pageno,$limit);
        if($rs_rolepayscheme->num_rows>0){
            while ($grow=$rs_rolepayscheme->fetch_array(MYSQLI_ASSOC)) {
                $rolepayscheme_array[] = $grow;
            }
        }else{
            throw new \Exception("No data found!",1);
        }
    }else{
        $rs_rolepayscheme=get_rolepayscheme($dbcon,$pageno,$limit);
        if($rs_rolepayscheme->num_rows>0){
            while ($grow=$rs_rolepayscheme->fetch_array(MYSQLI_ASSOC)) {
                $rolepayscheme_array[] = $grow;
            }
        }
    }

    if(count($rolepayscheme_array)>0){
        $response['error'] = false;
        $response['data']=$rolepayscheme_array;
    }else{
        throw new \Exception("No data found!",1);
    }


} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//gen_rolepayscheme(schemeno, schemetitle, userroleno, minpay, rate, perunit, duration, isprepaid)
//gen_userrolesetting (userroleno,userroletitle,description,ispublic)
function get_rolewise_rolepayscheme($dbcon,$userroleno,$pageno,$limit){
    $startindex=($pageno-1)*$limit;
    $sql = "SELECT schemeno, schemetitle,
                    minpay, rate, perunit, duration, isprepaid,
                    userroleno,(SELECT userroletitle FROM gen_userrolesetting WHERE userroleno=ps.userroleno) as userroletitle
            FROM gen_rolepayscheme as ps
            WHERE userroleno=?
            ORDER BY schemeno DESC
            LIMIT ?,?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $userroleno,$startindex,$limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function get_rolepayscheme($dbcon,$pageno,$limit){
    $startindex=($pageno-1)*$limit;
    $sql = "SELECT schemeno, schemetitle,
                    minpay, rate, perunit, duration, isprepaid,
                    userroleno,(SELECT userroletitle FROM gen_userrolesetting WHERE userroleno=ps.userroleno) as userroletitle
            FROM gen_rolepayscheme as ps
            ORDER BY schemeno DESC
            LIMIT ?,?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii",$startindex,$limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

?>
