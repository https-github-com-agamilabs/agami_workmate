<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try {
    //$schemeno
    $schemeno = -1;
    if (isset($_POST['schemeno']) && strlen($_POST['schemeno']) > 0) {
        $schemeno = (int)$_POST['schemeno'];
    }

    //$schemetitle,$userroleno,$minpay,$rate,$perunit,
    $schemetitle = NULL;
    if (isset($_POST['schemetitle']) && strlen($_POST['schemetitle']) > 0) {
        $schemetitle = trim(strip_tags($_POST['schemetitle']));
    }else{
        throw new \Exception("Title cannot be empty!", 1);
    }

    if (isset($_POST['userroleno']) && strlen($_POST['userroleno']) > 0) {
        $userroleno = (int)$_POST['userroleno'];
    }else{
        throw new \Exception("You must select a user-role!", 1);
    }

    $minpay = 0.0;
    if (isset($_POST['minpay']) && strlen($_POST['minpay']) > 0) {
        $minpay = (double)$_POST['minpay'];
    }

    $rate = 0.0;
    if (isset($_POST['rate']) && strlen($_POST['rate']) > 0) {
        $rate = (double)$_POST['rate'];
    }

    $perunit = 'MONTHLY';
    if (isset($_POST['perunit']) && strlen($_POST['perunit']) > 0) {
        $perunit = trim(strip_tags($_POST['perunit']));
    }

    $duration = 0;
    if (isset($_POST['duration']) && strlen($_POST['duration']) > 0) {
        $duration = (int) $_POST['duration'];
    }

    $isprepaid = 1;
    if (isset($_POST['isprepaid']) && strlen($_POST['isprepaid']) > 0) {
        $isprepaid = (int) $_POST['isprepaid'];
    }

    if ($schemeno > 0) {
        $unos = update_entry(
            $dbcon,
            $schemetitle,
            $userroleno,
            $minpay,
            $rate,
            $perunit,
            $duration,
            $isprepaid,
            $schemeno
        );
        if ($unos > 0) {
            $response['error'] = false;
            $response['message'] = "Successfully Modified.";
        } else {
            throw new \Exception("Could not modify! Check data and try again.", 1);
        }
    } else {
        $newpkno = add_entry(
            $dbcon,
            $schemetitle,
            $userroleno,
            $minpay,
            $rate,
            $perunit,
            $duration,
            $isprepaid
        );
        if ($newpkno > 0) {
            $response['error'] = false;
            $response['message'] = "Successfully Added.";
        } else {
            throw new \Exception("Could not add! Check data and try again.", 1);
        }
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//gen_rolepayscheme(schemeno, schemetitle, userroleno, minpay, rate, perunit, duration, isprepaid)
function add_entry(
    $dbcon,
    $schemetitle,
    $userroleno,
    $minpay,
    $rate,
    $perunit,
    $duration,
    $isprepaid
) {

    //date_default_timezone_set("Asia/Dhaka");
    //$entrydatetime = date("Y-m-d H:i:s");
    $sql = "INSERT INTO gen_rolepayscheme(schemetitle,userroleno,minpay,rate,perunit,
                                      duration,isprepaid)
            VALUES(?,?,?,?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param(
        "siddsii",
        $schemetitle,
        $userroleno,
        $minpay,
        $rate,
        $perunit,
        $duration,
        $isprepaid
    );
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();

    return $result;
}

//gen_rolepayscheme(schemeno, schemetitle, userroleno, minpay, rate, perunit, duration, isprepaid)
function update_entry(
    $dbcon,
    $schemetitle,
    $userroleno,
    $minpay,
    $rate,
    $perunit,
    $duration,
    $isprepaid,
    $schemeno
) {
    $sql = "UPDATE gen_rolepayscheme
            SET schemetitle=?,userroleno=?,minpay=?,rate=?,perunit=?,
                            duration=?,isprepaid=?
            WHERE schemeno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param(
        "siddsiii",
        $schemetitle,
        $userroleno,
        $minpay,
        $rate,
        $perunit,
        $duration,
        $isprepaid,
        $schemeno
    );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
