<?php




$path = __DIR__ . '\templateTest.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);


$template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
$template_description = isset($_POST['template_description']) ? $_POST['template_description'] : '';
$prompt1  = isset($_POST['prompt1']) ? $_POST['prompt1'] : '';
$prompt2  = isset($_POST['prompt2']) ? $_POST['prompt2'] : '';
$prompt3  = isset($_POST['prompt3']) ? $_POST['prompt3'] : '';
$prompt4  = isset($_POST['prompt4']) ? $_POST['prompt4'] : '';
$stil = isset($_POST['stil']) ? $_POST['stil'] : '';
$ton =  isset($_POST['ton']) ? $_POST['ton'] : '';
$typ = isset($_POST['typ']) ? $_POST['typ'] : '';
$subFolder = isset($_POST['subFolder']) ? $_POST['subFolder'] : '';
$folder = isset($_POST['folder']) ? $_POST['folder'] : '';

$newTemplate = [$template_name => [$template_description, $prompt1, $prompt2, $prompt3, $prompt4, $stil, $ton, $typ]];

$newData = array_merge($jsonData[$folder][$subFolder], $newTemplate);
$jsonData[$folder][$subFolder] = $newData;

$jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);

echo $jsonData;
