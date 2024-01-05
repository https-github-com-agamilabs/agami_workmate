<?php


// https://agamilabs.com/files/notices/2020-02-20/notice12342342.pdf
// https://agamilabs.com/files/notices/2020-02-20/notice34234234.pdf
// https://agamilabs.com/files/notices/2020-02-21/noticesdfdsfsdfsdf.pdf
// https://agamilabs.com/files/notices/2020-02-21/noticesdfdsfsdfsdf.pdf
// PUBLIC_DOMAIN+MAINDIRS+DATE_OF_UPLOAD+FILENAME

// session_start();

require "util_file.php";
$milliseconds = round(microtime(true) * 1000);

$message = '';
// if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload') {
if (isset($_FILES['file'])) {
    // var_dump($_FILES['file']);
    // get details of the uploaded file
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'] . random_string(10) . $milliseconds;
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $fileExtension = (isset($_POST['ext']) && count(explode('/', $_POST['ext'])) == 2) ? explode('/', $_POST['ext'])[1] : $fileExtension;

    // sanitize file-name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // check if file has one of the following extensions
    $allowedfileExtensions = array('blob', 'jpg', 'jpeg', 'gif', 'png', 'zip', 'txt', 'xls', 'xlsx', 'csv', 'json', 'doc', 'pdf');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        // directory in which the uploaded file will be moved
        //$uploadFileDir = '/uploaded_files/';

        $pc = getProjectConfig();

        $dest_path_root = $pc['files_root']; //dirname(__FILE__) . $uploadFileDir;

        if (!file_exists($dest_path_root)) {
            mkdir($dest_path_root, 0777);
        }

        // echo $dest_path_root ." = ".file_exists($dest_path_root);

        if (isset($_POST['location'])) {
            $location = trim(strip_tags($_POST['location']));

            $location_arr = explode('/', $location);

            $tp = $dest_path_root;

            for ($i = 0; $i < count($location_arr); $i++) {
                // code...

                if (strlen($location_arr[$i]) == 0) {
                    continue;
                }

                $tp = $tp . DIRECTORY_SEPARATOR . $location_arr[$i];

                $tp = normalisePath($tp);

                //$tp = implode(DIRECTORY_SEPARATOR, explode('/', $tp));

                // echo "<br>";
                // echo $dest_path_root;
                // echo "<br>";
                // echo $dest_path_root;

                if (!file_exists($tp)) {
                    mkdir($tp, 0777, true);
                }
            }

            $dest_path_root = $tp . DIRECTORY_SEPARATOR;
        }

        $dest_path = $dest_path_root . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $response['error'] = false;
            $response['dest_path'] = normalisePath($dest_path);
            $response['fileurl'] = normalisePath(generatePublicUrl($dest_path));

            $message = 'File is successfully uploaded.';
        } else {
            $response['error'] = true;

            $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
            $message.= "\n fileTmpPath: ".$fileTmpPath;
            $message.= "\n dest_path: ".$dest_path;
        }
    } else {
        $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        $response['error'] = true;
    }
} else {
    $message = 'There is some error in the file upload. Please check the following error.<br>';
    $message .= 'Error:' . $_FILES['file']['error'];
    $response['error'] = true;
}
// }
//$_SESSION['message'] = $message;

$response['message'] = $message;
echo json_encode($response);
