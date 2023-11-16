<?php


function check_task_status_image()
{

    $task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';

    $url = 'https://plugin.seo-kueche.de/api/chat/get_result/' . $task_id; // Adjust the URL as needed


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);  // Sets a timeout of 600 seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Seo-Header: ' . content_url())); // Set content type to JSON


    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        wp_send_json_error('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);

    $response_data = json_decode($response, true);
    // Check if task is complete (this is based on whatever logic or data the external server provides)
    if ($response_data['status'] == 'completed') {
        // Do something if needed, then send a positive response
        echo json_encode($response_data['answer']);
    } else {
        var_dump($response_data);
        // If the task is not yet complete, indicate so
        echo 'Task still processing.';
    }
    wp_die();
}
add_action('wp_ajax_check_task_status_image', 'check_task_status_image');

add_action('wp_ajax_gpt_create_image', 'gpt_create_image');
function gpt_create_image()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    $prompt = isset($_POST['image_prompt']) ? wp_kses_post($_POST['image_prompt']) : '';
    $premium = isset($_POST['premium']) ? $_POST['premium'] : "false";

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
    $jsonString = file_get_contents($path);
    $jsonArray = json_decode($jsonString, true);

    $apiKey = $jsonArray['apiKey'];

    if ($premium == "true") {
        // Logic when $premium is true, diverting to Flask App
        $url = 'https://plugin.seo-kueche.de/api/create_image'; // Replace with your Flask app URL
        $data = array(
            'prompt' =>  $prompt,
            "n" => 1,
            "size" => "1024x1024",
        );
        $jsonData = json_encode($data);
    } else {
        // Original logic if $premium is false
        $url = 'https://api.openai.com/v1/images/generations';
        $data = array(
            "model" => "dall-e-3",
            "quality" => "hd",
            'prompt' => ' ' . $prompt . ' ',
            "n" => 1,
            "size" => "1024x1024",
        );
        $jsonData = json_encode($data);
    }


    $ch = curl_init($url);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'Seo-Header: ' . content_url()
    ));


    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);


    if ($response) {
        echo $response;
        wp_die();
    } else {
        echo 'No response received.';
        wp_die();
    }
}
function convertBase64ToImage($base64Data, $outputPath)
{
    $data = base64_decode($base64Data);
    if ($data !== false) {
        file_put_contents(dirname(__DIR__) . '/' . $outputPath, $data);
        return true;
    } else {
        return false;
    }
}

add_action('wp_ajax_gpt_image_variation', 'gpt_image_variation');
function gpt_image_variation()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    $orgBase64 = isset($_POST['orgBase64']) ? $_POST['orgBase64'] : '';
    convertBase64ToImage($orgBase64, 'variation.png');
    $premium = isset($_POST['premium']) ? $_POST['premium'] : 'false';

    if (!class_exists('WP_Http')) {
        include_once(ABSPATH . WPINC . '/class-http.php');
    }


    $ch = curl_init();

    $image_path = dirname(__DIR__) . '/variation.png';

    if ($premium == "true") {
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://plugin.seo-kueche.de/api/image_variation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'image' => new CURLFile($image_path),
            "size" => "512x512",
        ));

        // Execute cURL session and handle response
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        echo $result;
    } else {
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/images/variations");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'image' => new CURLFile($image_path),
            "size" => "512x512",

        ));
        $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
        $jsonString = file_get_contents($path);
        $jsonArray = json_decode($jsonString, true);

        $apiKey = $jsonArray['apiKey'];

        $headers[] = "Authorization: Bearer " . $apiKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $apiKey, 'Content-Type: application/json', 'Seo-Header: ' . content_url()));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        echo $result;
    }

    curl_close($ch);
}

add_action('wp_ajax_gpt_edit_image', 'gpt_edit_image');
function gpt_edit_image()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    $orgBase64 = isset($_POST['orgBase64']) ? $_POST['orgBase64'] : '';
    $maskBase64 = isset($_POST['maskBase64']) ? $_POST['maskBase64'] : '';
    convertBase64ToImage($orgBase64, 'org.png');
    convertBase64ToImage($maskBase64, 'mask.png');
    $prompt = isset($_POST['prompt']) ? wp_kses_post($_POST['prompt']) : '';
    $premium = isset($_POST['premium']) ? $_POST['premium'] : 'false';

    // Prepare for a POST request to Flask endpoint
    $image_path = dirname(__DIR__) . '/org.png';
    $mask_path = dirname(__DIR__) . '/mask.png';

    $ch = curl_init();
    if ($premium == "true") {

        curl_setopt($ch, CURLOPT_URL, "https://plugin.seo-kueche.de/api/edit_image");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data', 'Seo-Header: ' . content_url()));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'image' => new CURLFile($image_path),
            'mask' => new CURLFile($mask_path),
            'prompt' => $prompt,
            "size" => "512x512",
        ));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        echo $result;
        curl_close($ch);
    } else {
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/images/edits");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'image' => new CURLFile($image_path),
            'mask' => new CURLFile($mask_path),
            'prompt' => $prompt,
            "size" => "512x512",

        ));
        $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
        $jsonString = file_get_contents($path);
        $jsonArray = json_decode($jsonString, true);

        $apiKey = $jsonArray['apiKey'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $apiKey, 'Content-Type: application/json'));


        $result = curl_exec($ch);

        echo $result;

        curl_close($ch);
    }


    wp_die();
}


add_action('wp_ajax_add_image', 'add_image');
function add_image()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    $description = 'some description';
    $files = isset($_POST['image_urls']) ? $_POST['image_urls'] : '';
    $count = isset($_POST['count']) ? $_POST['count'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';

    if (!class_exists('WP_Http')) {
        include_once(ABSPATH . WPINC . '/class-http.php');
    }


    for ($i = 1; $i <= $count; $i++) {
        $http = new WP_Http();

        $response = $http->request($files[$i - 1]);
        if ($response['response']['code'] !== 200) {
            echo "fail1";
            return false;
        }
        $upload = wp_upload_bits("$title.jpg", null, $response['body']);
        echo $upload['error'];
        echo $upload['file'];
        $file_path = $upload['file'];
        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_name, null);
        $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
        $wp_upload_dir = wp_upload_dir();

        $post_info = array(
            'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $file_type['type'],
            'post_title'     => $title,
            'post_excerpt' => "",
            'post_content'   => "",
            'post_status'    => 'inherit',
        );

        $attach_id = wp_insert_attachment($post_info, $file_path);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id,  $attach_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $title);
        echo $attach_id;
    }
}

add_action('wp_ajax_fetch_image_as_blob', 'fetch_image_as_blob');
function fetch_image_as_blob()
{

    $url = isset($_POST['url']) ? $_POST['url'] : '';

    // Initialize a new instance of WP_Http
    $http = new WP_Http();

    // Fetch the image
    $response = $http->get($url);

    // Check for WP_Error or if the request was not successful
    if (is_wp_error($response) || $response['response']['code'] != 200) {
        echo "fail1";
        return false;
    }

    // Return the image body (blob)
    $image_data = $response['body'];
    $mime_type = getimagesizefromstring($image_data)['mime'];
    $base64_encoded = base64_encode($image_data);
    echo "data:$mime_type;base64,$base64_encoded";
    wp_die();
}
