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

        $results = get_all_color($dbcon);

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

    //asp_priority(priorityno, prioritytitle, colorno)
    function get_all_color($dbcon){
        $sql = "SELECT colorno as id, colortitle as text
                FROM asp_color
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
