<?php

try {

  require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
} catch (\Throwable $th) {
  echo $th;
  try {
    //code...
    require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
  } catch (\Throwable $th) {
    echo $th;
  }
}
$prompt = isset($_POST['image_prompt']) ? wp_kses_post($_POST['image_prompt']) : '';
// DALL-E API endpoint
$url = 'https://api.openai.com/v1/images/generations';



// Data payload for the API request
$data = array(
  'prompt' => ' ' . $prompt . ' ',
  "n" => 1,
  "size" => "1024x1024"

);

// Convert the data payload to JSON
$jsonData = json_encode($data);

// cURL initialization
$ch = curl_init($url);

// Set the cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  'Authorization: Bearer ' . $apiKey
));

// Execute the cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
  echo 'Error:' . curl_error($ch);
}

// Close the cURL handle
curl_close($ch);

// Process the response
if ($response) {
  $responseData = json_decode($response, true);
  // Check if the "data" array exists and has at least one item
  if (isset($responseData['data']) && !empty($responseData['data'])) {
    // Access the first item's "url" key
    $url = $responseData['data'][0]['url'];
    echo $url;
  } else {
    echo 'No URL found in the response.';
  }
} else {
  echo 'No response received.';
}
