<?php


// Fügt das Bilder erstellen Menü in die obere Admin leiste ein



// Fügt das Bilder erstellen Menü in die Werkzeug leiste ein
add_action('admin_menu', 'nmd_image_page');

function nmd_image_page()
{

    add_submenu_page(
        'tools.php', // parent page slug
        'Bilder Erstellen',
        'Bilder Erstellen',
        'edit_posts',
        'nmd_create_image',
        'nmd_create_image_callback',
        0 // menu position
    );
}


function myAjax()
{
    wp_enqueue_script('my-ajax-script2', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_image_page.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script2', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
/*
 *  API Call an DALL-E zur Erstellung der Bilder
 */
function gpt_create_image()
{
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

    $url = 'https://api.openai.com/v1/images/generations';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
    $jsonString = file_get_contents($path);
    $jsonArray = json_decode($jsonString, true);

    $apiKey = $jsonArray['apiKey'];


    $data = array(
        'prompt' => ' ' . $prompt . ' ',
        "n" => 1,
        "size" => "1024x1024"

    );


    $jsonData = json_encode($data);


    $ch = curl_init($url);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));


    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);


    if ($response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['data']) && !empty($responseData['data'])) {
            $url = $responseData['data'][0]['url'];
            echo $url;
        } else {
            echo 'No URL found in the response.';
        }
    } else {
        echo 'No response received.';
    }
}


/*
 * Fügt Bild in die Mediathek ein 
 */
function add_image()
{
    try {
        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {
        echo $th;
        try {
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
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
add_action('wp_ajax_gpt_create_image', 'gpt_create_image');
add_action('wp_ajax_nopriv_gpt_create_image', 'gpt_create_image');
add_action('wp_ajax_add_image', 'add_image');
add_action('wp_ajax_nopriv_add_image', 'add_image');

add_action('scriptTest2', 'myAjax');
function nmd_create_image_callback()
{
    do_action('scriptTest2');

?>

    <div id="overlay">
        <center>
            <div class="lds-roller">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </center>
    </div>
    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/image_page.css">
    <div class="header">
        <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

            <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
        </a>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
            <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">Texte erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=image.php'); ?>" id="bilderErstellem">Bilder erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Meta-Daten</a>
            <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
        </nav>
    </div>
    <div class="background">




        <div class="wrap">

            <div class="mitte container">
                <div class="mainInputs">
                    <div style="display: flex; align-items: center; ">
                        <h1 class="wp-heading-inline">Bilder erstellen lassen</h1>


                    </div>
                    <div class="flex_button_header">

                        <div class="flexAlignMiddle">
                            <select name="count" id="count" aria-placeholder="Anzahl der Bilder">
                                <option value="" disabled selected>Anzahl der Bilder</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <input placeholder="Thema" name="nmd_image_prompt" id="nmd_image_prompt"></input>
                            <button class="button action" onclick="create_image()">Generieren</button>
                            <button class="button action" onclick="add_image()">Erzeugte Bilder hinzufügen</button>

                        </div>




                    </div>
                    <div class="bilder">
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_1" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">
                        </div>
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_2" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">

                        </div>
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_3" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>

    </script>

<?php

}
