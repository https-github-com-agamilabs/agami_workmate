<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
$response['error'] = false;
$response['message'] = '';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    //storyphaseno, orgno, storyphasetitle, colorno
    $storyphaseno = -1;
    if (isset($_POST['storyphaseno']) && strlen($_POST['storyphaseno']) > 0) {
        $storyphaseno = (int) $_POST['storyphaseno'];
    }

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected!!", 1);
    }

    if (isset($_POST['storyphasetitle']) && strlen($_POST['storyphasetitle']) > 0) {
        $storyphasetitle = trim(strip_tags($_POST['storyphasetitle']));
    } else {
        throw new Exception("Title cannot be empty!!", 1);
    }

    $color = NULL;
    if (isset($_POST['colorno']) && strlen($_POST['colorno']) > 0) {
        $colorno = (int) $_POST['colorno'];
    }


    if ($storyphaseno > 0) {
        $unos = update_storyphase($dbcon,  $storyphasetitle, $colorno, $orgno, $storyphaseno);

        if ($unos == 0) {
            throw new Exception("Could not update!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Updated successfully.';
        }
    } else {
        $inos = add_storyphase($dbcon, $orgno, $storyphasetitle, $colorno);

        if ($inos == 0) {
            throw new Exception("Could not add!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Added successfully.';
        }
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//asp_storyphase(storyphaseno, orgno, storyphasetitle, colorno)
function add_storyphase($dbcon, $orgno, $storyphasetitle, $colorno)
{
    $sql = "INSERT INTO asp_storyphase(orgno, storyphasetitle, colorno)
            VALUES(?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("isi", $orgno, $storyphasetitle, $colorno);

    if ($stmt->execute()) {
        $result = $stmt->insert_id;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}

function update_storyphase($dbcon,  $storyphasetitle, $colorno, $orgno, $storyphaseno)
{
    $sql = "UPDATE asp_storyphase
            SET storyphasetitle=?, colorno=?
            WHERE orgno=? AND storyphaseno=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("siii", $storyphasetitle, $colorno, $orgno, $storyphaseno);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}
