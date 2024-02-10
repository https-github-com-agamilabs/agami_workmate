<?php
    $base_path = dirname(dirname(dirname(dirname(__FILE__))));
    include_once  $base_path."/ui/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    require_once($base_path."/db/Database.php");
    $db = new Database();
    $dbcon=$db->db_connect();
    if (!$db->is_connected()) {
        $response['error'] = true;
        $response['message'] = "Database is not connected!";
        echo json_encode($response);
        exit();
    }

    try {

        if(isset($_SESSION['orgno']) && strlen($_SESSION['orgno'])>0){
            $orgno=(int) $_SESSION['orgno']);
        }else{
            throw new \Exception("You must proceed with an organization!", 1);
        }

        $results = get_filtered_storytype($dbcon, $orgno);

        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $results_array[] = $row;
            }
        }
        $response['results'] = $results_array;

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */

    //asp_storyphase(storyphaseno, storyphasetitle, colorno)
    function get_filtered_storytype($dbcon, $orgno){
        $sql = "SELECT storyphaseno, storyphasetitle,
                        colorno, (SELECT colorcode FROM asp_color WHERE colorno=p.colorno) as colorcode
                FROM asp_storyphase as p
                WHERE orgno=?
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
