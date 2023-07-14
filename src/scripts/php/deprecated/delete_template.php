<?php




$path = __DIR__ . '\templateTest.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);
$folder = "";
$subFolder = "";
$index = "";

try {

    $folder = isset($_POST['folder']) ? $_POST['folder'] : '';
    $subFolder = isset($_POST['subFolder']) ? $_POST['subFolder'] : '';
    $index = isset($_POST['index']) ? $_POST['index'] : '';
    $typ = isset($_POST['typ']) ? $_POST['typ'] : '';
} catch (Exception $e) {
}

if ($typ == 'prompt') {

    unset($jsonData[$folder][$subFolder][$index]);
} elseif ($typ == 'sub') {
    unset($jsonData[$folder][$subFolder]);
} elseif ($typ == 'folder') {
    unset($jsonData[$folder]);
}


$jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);

echo $jsonData;
