


<?php
// Read the JSON file
//$jsonData = file_get_contents('path/to/your/file.json');

//$arrayData='34567';
function langConverter($lang, $filename)
{
    $filepath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lang-json' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $filename . '.json';

    if (!file_exists($filepath)) {
        echo $filepath;
        echo "Not found";
        exit();
    }else{
        // $jsonData = file_get_contents($filepath, true);

        // echo $filepath;
        // echo "found";
        // var_dump($jsonData);
        // exit();
    }
    $jsonData = file_get_contents($filepath);

    // Convert JSON to PHP array
    $arrayData = json_decode($jsonData, true);

    // Access the array elements
    // echo $arrayData['en']['lang_about_us'];  // Output: "About us"
    //echo $arrayData['bn']['lang_about_us'];  // Output: "আমাদের সম্পর্কে"
    //print_r($arrayData['bn']);
    return $arrayData;
}

//langConverter('../lang-json/about.json');
