<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected!!", 1);
    }

    if (isset($_POST['storyphaseno']) && strlen($_POST['storyphaseno']) > 0) {
        $storyphaseno = (int) $_POST['storyphaseno'];
    } else {
        throw new Exception("Task-type must be selected!!", 1);
    }

    $result = delete_a_storyphase($dbcon, $orgno, $storyphaseno);

    if ($result > 0) {
        $response['error'] = false;
        $response['message'] = 'Removed successfully!';
    } else {
        $response['error'] = true;
        $response['message'] = 'Data Error! Check the data.';
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//asp_storyphase(storyphaseno, orgno, storyphasetitle, colorno)
function delete_a_storyphase($dbcon, $orgno, $storyphaseno)
{
    $sql = "DELETE
            FROM asp_storyphase
            WHERE orgno=? AND storyphaseno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno, $storyphaseno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
