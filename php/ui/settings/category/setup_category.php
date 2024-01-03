<?php
    include_once  dirname(dirname(dirname(__FILE__)))."/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try {

        $base_path = dirname(dirname(dirname(dirname(__FILE__))));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        $catno=-1;
        if (isset($_POST['catno'])) {
            $catno = (int) $_POST['catno'];
        }

        if (isset($_POST['cattitle']) && strlen($_POST['cattitle'])>0) {
            $cattitle = trim(strip_tags($_POST['cattitle']));
        }else{
            throw new \Exception("Category Title cannot be Empty!", 1);
        }

        if($catno>0){
            $nos=update_category($dbcon, $cattitle, $catno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "Category info is Updated.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Update Category info.";
            }
        }else{
            $catno=insert_category($dbcon, $cattitle);
            if($catno>0){
                $response['error'] = false;
                $response['message'] = "Category info is Added.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add Category info.";
            }
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);

    $dbcon->close();

    /**
     * Local Function
     */

    function insert_category($dbcon, $cattitle){

        $sql = "INSERT INTO msg_category(cattitle)
                VALUES(?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("s", $cattitle);
        $stmt->execute();
        return $stmt->insert_id;


    }

    function update_category($dbcon, $cattitle, $catno){
        $sql = "UPDATE msg_category
                SET cattitle=?
                WHERE catno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $cattitle, $catno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
