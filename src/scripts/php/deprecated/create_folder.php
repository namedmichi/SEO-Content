<?php

$name = isset($_POST['name']) ? $_POST['name'] : '';

$path = __DIR__ . '\templateTest.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);

$jsonData[$name] = [];

$jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);

echo $jsonData;
