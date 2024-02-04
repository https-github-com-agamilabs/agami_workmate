<?php
    include_once  dirname(dirname(__FILE__))."/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try {
        $base_path = dirname(dirname(dirname(__FILE__)));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        $pageno=1;
        if (isset($_POST['pageno'])) {
            $pageno = (int) $_POST['pageno'];
        }

        $limit=10;
        if (isset($_POST['limit'])) {
            $limit = (int) $_POST['limit'];
        }

        $list = get_users($dbcon, $pageno, $limit);

        if ($list->num_rows > 0) {
            $meta_array = array();
            while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            $response['error'] = false;
            $response['results'] = $meta_array;

        } else {
            $response['error'] = true;
            $response['results'] = array();
            $response['message'] = "No User Found!";
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */

    function get_users($dbcon, $pageno, $limit){
        $startindex=($pageno-1)*$limit;
        $sql = "SELECT userno,username,firstname,lastname,photo_url,
                        email,primarycontact
                FROM hr_user 
                ORDER BY firstname,lastname
                LIMIT ?,?";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $startindex,$limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;

    }


