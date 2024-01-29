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

if (isset($_POST['tags'])) {
    $tags = $_POST['tags'];
} else {
    $tags = "";
}

if (isset($_POST['pageno'])) {
    $pageno = (int)$_POST['pageno'];
} else {
    $pageno = 1;
}

if (isset($_POST['limit'])) {
    $limit = (int)$_POST['limit'];
} else {
    $limit = 3;
}


$filter_doctno = -1;
if (isset($_POST['filter_doctno'])) {
    $filter_doctno = (int)$_POST['filter_doctno'];
}

$query = "";
if (isset($_POST['query'])) {
    $query = trim(strip_tags($_POST['query']));
}

//===   End of Input Management   ===//

//===   Begin of Processing   ===//

// if (!isset($login_doctno) && !isset($login_patientno)) {
//     $response['error'] = true;
//     $response['message'] = "You have to login";
//     echo json_encode($response);
//     exit();
// }

$posts = array();

$result = getBlogPosts($dbcon, $filter_doctno, $pageno, $limit, $tags, $query);

if ($result->num_rows < 1) {
    $response['error'] = true;
    $response['message'] = $pageno > 1 ? "Oops! Seems we got no more post." : "Oops! Seems we got no post yet.";
} else {
    $slno = 0;
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $postno = $row['postno'];

        $postTags = array();
        $postTagsRes = getBlogPostTags($dbcon, $postno);
        while ($aTag = $postTagsRes->fetch_array(MYSQLI_ASSOC)) {
            $postTags[] = $aTag;
        }
        $row['tags'] =  $postTags;

        // files
        $postFiles = array();
        $postFilesRes = getBlogPostFiles($dbcon, $postno);
        while ($aFile = $postFilesRes->fetch_array(MYSQLI_ASSOC)) {
            $postFiles[] = $aFile;
        }
        $row['files'] = $postFiles;

        $posts[] = $row;
    }
    $response['error'] = false;
    $response['data'] = $posts;
}

echo json_encode($response);
exit();

//===   End of Processing   ===//

//===   Beging of Output in JSON   ===//
//===   End of Output in JSON  ===//
$dbcon->close();

function getBlogPosts($dbcon, $filter_doctno, $pageno, $limit, $tags, $query)
{

    if (strlen($tags)) {
        $tags_arr = array_map('intval', explode(',', $tags));

        // var_dump($tags_arr);

        // $tagSelection = " AND postno in (SELECT postno FROM blg_blogposttags WHERE tagno in ($tags)) ";

        // sql injection safe
        // $tagSelection = ` AND postno in (SELECT postno FROM blg_blogposttags WHERE tagno in ("'" + REPLACE( ? ,',',''',''') + "'") )`;
        $tagSelection = " AND postno in (SELECT postno FROM blg_blogposttags WHERE tagno in (" . implode(",", $tags_arr) . ") )";
    } else {
        $tagSelection = "";
    }

    if (isset($filter_doctno) && $filter_doctno > 0) {
        $doctorFilterSql = " WHERE doctno='$filter_doctno'";
    } else {
        $doctorFilterSql = "";
    }

    $keys = explode(" ", $query);
    $search = implode("%", $keys);;
    $search = '%' . $search . '%';

    $start = ($pageno - 1) * $limit;
    $sql = "SELECT *
            FROM blg_blogpost as bp
            inner join
            (select doctno, peopleno, designation, affiliation, diploma from drrx_doctorinfo $doctorFilterSql) as di
            on bp.postauthorno=di.doctno
            inner join
            (select peopleno, concat(ifnull(firstname, ''), ' ', ifnull(lastname, '')) as fullname, firstname, lastname, primarycontact, photo_url from gen_peopleprimary) as pp
            on pp.peopleno=di.peopleno
            WHERE post LIKE ? $tagSelection
            order by posttime desc
            LIMIT ?, ?";
    // echo $sql;

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sii", $search, $start, $limit);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function getBlogPostTags($dbcon, $postno)
{
    $sql = "SELECT
                tagno,
                (SELECT tagtitle FROM blg_tags WHERE tagno=a.tagno) as tagtitle
            FROM blg_blogposttags as a
            WHERE postno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $postno);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function getBlogPostFiles($dbcon, $postno)
{
    $sql = "SELECT fileurl
            FROM blg_blogpostfiles
            WHERE postno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $postno);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
