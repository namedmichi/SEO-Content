<?php
$path = __DIR__ . '\settings.json';
$apiKey = isset($_POST['apiKey']) ? $_POST['apiKey'] : '';
$firmenname = isset($_POST['firmenname']) ? $_POST['firmenname'] : '';
$adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';

$settingsArray = array('apiKey' => $apiKey, 'firmenname' => $firmenname, 'adresse' => $adresse);
$jsonData = json_encode($settingsArray, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);
