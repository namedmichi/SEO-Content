<?php
add_action('wp_ajax_update_meta_page', 'update_meta_page');
function update_meta_page()
{

    //Laden der Wordpress Funktionen.
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }
    $pageId = isset($_POST['pageId']) ? $_POST['pageId'] : '';
    $newTitle = isset($_POST['newTitle']) ? $_POST['newTitle'] : '';
    $newExcerpt = isset($_POST['newExcerpt']) ? $_POST['newExcerpt'] : '';

    // Update the page using WordPress functions
    $pageData = array(
        'ID' => $pageId,
        'post_title' => $newTitle,
        'post_excerpt' => $newExcerpt,
    );
    update_page_meta_description($pageId, $newExcerpt);
    // Update the page
    $result = wp_update_post($pageData);

    // Check if the page was updated successfully
    if ($result !== 0 && !is_wp_error($result)) {
        // Update the Yoast SEO meta description



        // Update the meta description using Yoast SEO function
        update_post_meta($pageId, '_yoast_wpseo_metadesc', $newExcerpt);

        echo 'Page updated successfully!';
    } else {
        echo 'Error updating page.';
    }

    wp_die();
}

function update_page_meta_description($page_id, $new_meta_description)
{
    // Perform database operations to update the meta description for a specific page.
    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_metas';

    $wpdb->update(
        $table_name,
        array('meta_description' => $new_meta_description), // new values
        array('page_id' => $page_id) // where clause
    );
}



add_action('wp_ajax_delete_template_meta', 'delete_template_meta');
function delete_template_meta()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
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
}

add_action('wp_ajax_create_folder_meta', 'create_folder_meta');
function create_folder_meta()
{

    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData[$name] = [];

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    echo file_put_contents($path, $jsonData);
}
add_action('wp_ajax_edit_folder_meta', 'edit_folder_meta');
function edit_folder_meta()
{

    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }

    function changeKey_meta($array, $oldKey, $newKey)
    {
        // Check if the old key exists
        if (array_key_exists($oldKey, $array)) {
            // Change the key
            $keys = array_keys($array);
            $keys[array_search($oldKey, $keys)] = $newKey;

            // Reassemble the array
            $array = array_combine($keys, array_values($array));
        }

        // Process nested arrays
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = changeKey_meta($value, $oldKey, $newKey);
            }
        }

        return $array;
    }
    $folder = isset($_POST['folder']) ? $_POST['folder'] : '';
    $newName = isset($_POST['newName']) ? $_POST['newName'] : '';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData = changeKey_meta($jsonData, $folder, $newName);


    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    echo file_put_contents($path, $jsonData);
}

add_action('wp_ajax_create_sub_folder_meta', 'create_sub_folder_meta');
function create_sub_folder_meta()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $subName = isset($_POST['subName']) ? $_POST['subName'] : '';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData[$name][$subName] = [];

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);

    echo $jsonData;
    wp_die();
}

add_action('wp_ajax_save_template_meta', 'save_template_meta');
function save_template_meta()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }
    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);


    $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
    $template_description = isset($_POST['template_description']) ? $_POST['template_description'] : '';
    $prompt1  = isset($_POST['prompt1']) ? $_POST['prompt1'] : '';
    $prompt2  = isset($_POST['prompt2']) ? $_POST['prompt2'] : '';
    $subFolder = isset($_POST['subFolder']) ? $_POST['subFolder'] : '';
    $folder = isset($_POST['folder']) ? $_POST['folder'] : '';

    $newTemplate = [$template_name => [$template_description, $prompt1, $prompt2]];

    $newData = array_merge($jsonData[$folder][$subFolder], $newTemplate);
    $jsonData[$folder][$subFolder] = $newData;

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);

    echo $jsonData;
}
add_action('wp_ajax_get_template_meta', 'get_prompt_template_meta');
function get_prompt_template_meta()
{
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
        } catch (\Throwable $th) {
        }
    }
    $path = content_url() . '/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $jsonString = file_get_contents($path);
    echo $jsonString;
}



// Füge deine benutzerdefinierte Hook zu WordPress hinzu
add_action('update_seocontent_templates_hook', 'update_seocontent_template');

// Definiere eine benutzerdefinierte Hook
function update_seocontent_template_meta()
{
    // Pfad zur JSON-Datei in deinem Plugin-Verzeichnis
    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/metaPromptTemplates.json';

    // Lese den Inhalt der JSON-Datei
    $json_data = file_get_contents($json_file_path);

    // Konvertiere JSON in ein PHP-Array
    $settings = json_decode($json_data, true);

    // Aktualisiere die Option "seocontent_settings" mit den neuen JSON-Daten
    update_option('seocontent_templates_meta', $settings);
    wp_die(); // Beende die AJAX-Anfrage
}



function import_seocontent_template_meta()
{

    $meta  = isset($_POST['meta']) ? $_POST['meta'] : '';

    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/metaPromptTemplates.json';

    $jsonData = json_encode($meta, JSON_PRETTY_PRINT);
    file_put_contents($json_file_path, $jsonData);

    update_option('seocontent_templates_meta', $meta);
    wp_die();
}



add_action('wp_ajax_import_seocontent_template_meta_action', 'import_seocontent_template_meta');
