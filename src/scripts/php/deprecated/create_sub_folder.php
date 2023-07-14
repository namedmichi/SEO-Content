<?php

$name = isset($_POST['name']) ? $_POST['name'] : '';
$subName = isset($_POST['subName']) ? $_POST['subName'] : '';

$path = __DIR__ . '\templateTest.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);

$jsonData[$name][$subName] = [];

$jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);

echo $jsonData;
