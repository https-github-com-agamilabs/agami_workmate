


<?php
// Read the JSON file
//$jsonData = file_get_contents('path/to/your/file.json');

//$arrayData='34567';
function langConverter($path)
{
    $jsonData = file_get_contents($path);

    // Convert JSON to PHP array
    $arrayData = json_decode($jsonData, true);

    // Access the array elements
    // echo $arrayData['en']['lang_about_us'];  // Output: "About us"
    //echo $arrayData['bn']['lang_about_us'];  // Output: "আমাদের সম্পর্কে"
    //print_r($arrayData['bn']);
    return $arrayData;
}

//langConverter('../lang-json/about.json');
