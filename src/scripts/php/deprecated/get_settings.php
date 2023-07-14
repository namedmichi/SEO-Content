<?php

$path = __DIR__ . '\settings.json';
$jsonString = file_get_contents($path);
echo $jsonString;
