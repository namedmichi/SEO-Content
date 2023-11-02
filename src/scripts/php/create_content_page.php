<?php



add_action('admin_menu', 'rudr_submenu');
function rudr_submenu()
{

    add_submenu_page(
        'tools.php', // parent page slug
        'Texte Erstellen',
        'Texte Erstellen',
        'edit_posts',
        'nmd_create_content',
        'nmd_create_content_callback',
        0 // menu position
    );
}



function testScript()
{
    wp_enqueue_script('my-ajax-script', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_content_page_chat.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
/*
 * Erstellt eine neue Seite über "Content Erstellen"
 */
function gpt_create_post()
{
    try {
        //code...
        $thema = isset($_POST['thema']) ? wp_kses_post($_POST['thema']) : '';
        $title = isset($_POST['title']) ? wp_kses_post($_POST['title']) : '';
        $inhalt  = isset($_POST['inhalt']) ? $_POST['inhalt'] : '';
        $excerpt  = isset($_POST['excerpt']) ? wp_kses_post($_POST['excerpt']) : '';
        $typ = isset($_POST['typ']) ? wp_kses_post($_POST['typ']) : '';
        if ($typ == "page") {
            echo "The variable \$typ is 'page'.";
        } elseif ($typ == "post") {
            echo "The variable \$typ is 'post'.";
        } else {
            echo "The variable \$typ is neither 'page' nor 'post'.";
        }
        $wordpress_post = array(
            'post_title' => $title,
            'post_content' => $inhalt,
            'post_status' => 'draft',
            'post_author' => 1,
            'post_type' => $typ,
            'post_excerpt' => $excerpt,
        );


        $id = wp_insert_post($wordpress_post);
        save_page_meta_description($id, $excerpt);
        if ($typ == "page") {
            echo "The variable \$typ is 'page'.";
            echo $thema;
            $updated_post = array(
                'ID'        => $id,
                'post_name' => $thema,
            );
            wp_update_post($updated_post);
        }
        echo $id;
    } catch (\Throwable $th) {
        echo $th;
    }
    // Check if the page was updated successfully
    if ($id !== 0 && !is_wp_error($id)) {
        // Update the Yoast SEO meta description



        // Update the meta description using Yoast SEO function
        update_post_meta($id, '_yoast_wpseo_metadesc', $excerpt);

        echo 'Page updated successfully!';
    } else {
        echo 'Error updating page.';
    }
    try {
        //code...
    } catch (\Throwable $th) {
        //throw $th;
    }
}


function save_page_meta_description($page_id, $meta_description)
{
    // Perform database operations to save the meta description for a specific page.
    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_metas';

    $wpdb->insert(
        $table_name,
        array(
            'page_id' => $page_id,
            'meta_description' => $meta_description
        )
    );
}


/*
 *  Fügt ein Script Tag mit dem JSON-LD Code in die Seite ein
 */
function gpt_add_script_to_post()
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
    $postId = isset($_POST['response']) ? wp_kses_post($_POST['response']) : '';
    $faqOutput = isset($_POST['faq']) ? wp_kses_post($_POST['faq']) : '';

    save_page_script($postId, $faqOutput);
    wp_die();
}


function save_page_script($page_id, $script_content)
{
    $script_content = str_replace('\\', '', $script_content);
    echo $script_content;
    // Perform database operations to save the script tag content for a specific page.
    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_faqs';
    echo $table_name;
    $wpdb->insert(
        $table_name,
        array(
            'page_id' => $page_id,
            'script_content' => $script_content
        )
    );
    echo $wpdb->get_row("SELECT * FROM $table_name WHERE page_id = $page_id");
    wp_die();
}



/*
 *  Löscht einen Prompt aus dem Prompttemplates
 */
function delete_template()
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

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
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
/*
 *  Erstellt einen Ordnernamen im Prompttemplate
 */
function create_folder()
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

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData[$name] = [];

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    echo file_put_contents($path, $jsonData);
}
function edit_folder()
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

    function changeKey($array, $oldKey, $newKey)
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
                $array[$key] = changeKey($value, $oldKey, $newKey);
            }
        }

        return $array;
    }
    $folder = isset($_POST['folder']) ? $_POST['folder'] : '';
    $newName = isset($_POST['newName']) ? $_POST['newName'] : '';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData = changeKey($jsonData, $folder, $newName);


    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    echo file_put_contents($path, $jsonData);
}

/*
 *  Erstellt einen Unterordner im Prompttemplate
 */
function create_sub_folder()
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

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $jsonData[$name][$subName] = [];

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);

    echo $jsonData;
    wp_die();
}
/*
 *  Erstellt einen Prompt im Prompttemplate
 */
function save_template()
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
    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
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
}
/*
 * Lädt ein Prompt aus dem Prompttemplate
 */
function get_prompt_template()
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
    $path = content_url() . '/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    echo $jsonString;
}

function get_keyword_api()
{

    $topic = isset($_POST['topic']) ? $_POST['topic'] : '';

    $url = 'http://94.130.105.89/api/getKeyword';

    // Data to be sent in the body of the POST request
    $data = array(
        'topic' => $topic
    );

    // Convert array to JSON string
    $jsonData = json_encode($data);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Set content type to JSON

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Handle error
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);
    header('Content-Type: application/json');
    echo $response;
    wp_die();
}

function get_best_keyword_api()
{
    $urlArray = isset($_POST['urlArray']) ? $_POST['urlArray'] : '';

    $url = 'http://94.130.105.89/api/get_best_keyword';

    // Data to be sent in the body of the POST request
    $data = array(
        'urlArray' => $urlArray
    );

    // Convert array to JSON string
    $jsonData = json_encode($data);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Set content type to JSON

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Handle error
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);
    header('Content-Type: application/json');
    echo $response;
    wp_die();
}

function askGPT()
{
    $chat = isset($_POST['chat']) ? $_POST['chat'] : '';
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '';
    // API URL for the POST request
    $url = 'http://94.130.105.89/api/chat';

    // Data to be sent in the body of the POST request
    $data = array(
        'messages' => $chat,
        'model' => $model,
        'temperature' => $temperature
    );

    // Convert array to JSON string
    $jsonData = json_encode($data);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST1
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set POST data
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);  // Sets a timeout of 600 seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Set content type to JSON

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Handle error
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Use the API response
    echo $response;

    wp_die();
}
function getTokens()
{

    // API URL for the GET request
    $url = 'http://94.130.105.89/api/get_tokens';

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return response as a string

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Handle error
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Use the API response
    echo $response;
    wp_die();
}

add_action('scriptTest', 'testScript');
add_action('wp_ajax_ask_gpt', 'askGPT');
add_action('wp_ajax_get_tokens', 'getTokens');
add_action('wp_ajax_gpt_create_post', 'gpt_create_post');
add_action('wp_ajax_nopriv_gpt_create_post', 'gpt_create_post');
add_action('wp_ajax_my_ajax_request2', 'gpt_add_script_to_post');
add_action('wp_ajax_nopriv_my_ajax_request2', 'gpt_add_script_to_post');
add_action('wp_ajax_delete_template', 'delete_template');
add_action('wp_ajax_nopriv_delete_template', 'delete_template');
add_action('wp_ajax_create_folder', 'create_folder');
add_action('wp_ajax_nopriv_create_folder', 'create_folder');
add_action('wp_ajax_create_sub_folder', 'create_sub_folder');
add_action('wp_ajax_nopriv_create_sub_folder', 'create_sub_folder');
add_action('wp_ajax_save_template', 'save_template');
add_action('wp_ajax_nopriv_save_template', 'save_template');
add_action('wp_ajax_get_template', 'get_prompt_template');
add_action('wp_ajax_nopriv_get_template', 'get_prompt_template');
add_action('wp_ajax_edit_folder', 'edit_folder');
add_action('wp_ajax_nopriv_edit_folder', 'edit_folder');
add_action('wp_ajax_edit_sub_folder', 'edit_sub_folder');
add_action('wp_ajax_nopriv_edit_sub_folder', 'edit_sub_folder');
add_action('wp_ajax_get_keyword_api', 'get_keyword_api');
add_action('wp_ajax_nopriv_get_keyword_api', 'get_keyword_api');
add_action('wp_ajax_get_best_keyword_api', 'get_best_keyword_api');
add_action('wp_ajax_nopriv_get_best_keyword_api', 'get_best_keyword_api');

/*
 * Aktionen für Ajax Requests von anderen Seiten. 
 */
add_action('wp_ajax_get_main_settings', 'get_main_settings');
add_action('wp_ajax_nopriv_get_main_settings', 'get_main_settings');
add_action('wp_ajax_save_main_settings', 'save_main_settings');
add_action('wp_ajax_nopriv_save_main_settings', 'save_main_settings');
add_action('wp_ajax_update_meta_page', 'update_meta_page');
add_action('wp_ajax_nopriv_update_meta_page', 'update_meta_page');
add_action('wp_ajax_save_variable_settings', 'save_variable_settings');
add_action('wp_ajax_nopriv_save_variable_settings', 'save_variable_settings');
/*
 * Speichert die Einstellungen der Hauptseite 
 */
function save_variable_settings()
{

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/variables.json';

    $variables = isset($_POST['variables']) ? $_POST['variables'] : '';
    $jsonData = json_encode($variables, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);
    wp_die();
}


/*
 * Speichert die Einstellungen der Hauptseite 
 */
function save_main_settings()
{
    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
    $apiKey = isset($_POST['apiKey']) ? $_POST['apiKey'] : '';
    $firmenname = isset($_POST['firmenname']) ? $_POST['firmenname'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
    $Gewerbe = isset($_POST['Gewerbe']) ? $_POST['Gewerbe'] : '';
    $whyUs = isset($_POST['whyUs']) ? $_POST['whyUs'] : '';
    $usps = isset($_POST['usps']) ? $_POST['usps'] : '';
    $cta = isset($_POST['cta']) ? $_POST['cta'] : '';
    $shortcode = isset($_POST['shortcode']) ? $_POST['shortcode'] : '';
    $settingsArray = array('apiKey' => $apiKey, 'firmenname' => $firmenname, 'adresse' => $adresse, 'Gewerbe' => $Gewerbe, 'warumWir' => $whyUs, 'usps' => $usps, 'cta' => $cta, 'shortcode' => $shortcode);
    $jsonData = json_encode($settingsArray, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);
    wp_die();
}
/*
 * Läd die Einstellungen der Hauptseite 
 */
function get_main_settings()
{
    $path = content_url() . '/plugins/SEOContent/src/scripts/php/settings.json';
    $jsonString = file_get_contents($path);
    echo $jsonString;
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
/*
 * Aktualisiert die Meta Daten einer Seite
 */
function update_meta_page()
{

    //Laden der Wordpress Funktionen.
    try {

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
    } catch (\Throwable $th) {

        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
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




/*
*  Erstellt die AJAX actions für Meta-Daten
*/






/*
 *  Löscht einen Prompt aus dem Prompttemplates
 */
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
/*
 *  Erstellt einen Ordnernamen im Prompttemplate
 */
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


add_action('wp_ajax_delete_template_meta', 'delete_template_meta');
add_action('wp_ajax_nopriv_delete_template_meta', 'delete_template_meta');
add_action('wp_ajax_create_folder_meta', 'create_folder_meta');
add_action('wp_ajax_nopriv_create_folder_meta', 'create_folder_meta');
add_action('wp_ajax_create_sub_folder_meta', 'create_sub_folder_meta');
add_action('wp_ajax_nopriv_create_sub_folder_meta', 'create_sub_folder_meta');
add_action('wp_ajax_save_template_meta', 'save_template_meta');
add_action('wp_ajax_nopriv_save_template_meta', 'save_template_meta');
add_action('wp_ajax_get_template_meta', 'get_prompt_template_meta');
add_action('wp_ajax_nopriv_get_template_meta', 'get_prompt_template_meta');
add_action('wp_ajax_edit_folder_meta', 'edit_folder_meta');
add_action('wp_ajax_nopriv_edit_folder_meta', 'edit_folder_meta');
add_action('wp_ajax_edit_sub_folder_meta', 'edit_sub_folder_meta');
add_action('wp_ajax_nopriv_edit_sub_folder_meta', 'edit_sub_folder_meta');


/*
 *  Erstellt einen Unterordner im Prompttemplate
 */
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
/*
 *  Erstellt einen Prompt im Prompttemplate
 */
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
/*
 * Lädt ein Prompt aus den Prompttemplate
 */
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








function nmd_create_content_callback()
{
    do_action('scriptTest');


    $testPath = content_url() . '/plugins/SEOContent/src/scripts/php/templateTest.json';
    $promptPath = content_url() . '/plugins/SEOContent/src/scripts/php/prompts.json';
    try {


        $jsonString = file_get_contents($promptPath);
        $hardPrompts = json_decode($jsonString, true);
        $jsonString = file_get_contents($testPath);
        $testTemplates = get_option('seocontent_templates');
    } catch (Exception $e) {
    }




?>

    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/content_page.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">


    <div id="overlay">

        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <p style="position: absolute;top: 16px;left: 16px;">Loading</p>
            <span class="overlayBackground">
                <p id="loadingText" style="margin-bottom: 82px;">some Text</p>
            </span>
        </div>

    </div>
    <div class="background">
        <div class="header">
            <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

                <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
            </a>
            <nav>
                <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
                <a href="<?php echo admin_url('admin.php?page=content.php'); ?>" id="textErstellen">Texte erstellen</a>
                <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">Bilder erstellen</a>
                <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Meta-Daten</a>
                <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
            </nav>
        </div>




        <div id="infoText" class="infoText">
            <p>Tipps zur Erstellung optimaler Inhalte erhalten Sie in unserem Ratgeber: <a href="https://www.seo-kueche.de/ratgeber/">https://www.seo-kueche.de/ratgeber/</a></p>
        </div>
        <div class="contentContainer">
            <h1 class="wp-heading-inline"> Texte erstellen</h1>
            <div class="buttonBar">
                <input placeholder="Thema" name="nmd_topic_input" id="nmd_topic_input"></input>
                <select id="nmd_stil_select" name="nmd_stil_select">
                    <option value="" disabled selected>Schreibstil</option>
                    <option value="serious">Seriös</option>
                    <option value="authoritative">Bestimmt</option>
                    <option value="emotional">Emotional</option>
                    <option value="empathetic">Empathisch</option>
                    <option value="formal">Formell</option>
                    <option value="friendly">Freundlich</option>
                    <option value="humorous">Humorvoll</option>
                    <option value="informal">Informell</option>
                    <option value="ironic">Ironisch</option>
                    <option value="cold">Kalt</option>
                    <option value="clinical">Klinisch</option>
                    <option value="optimistic">Optimistisch</option>
                    <option value="pessimistic">Pessimistisch</option>
                    <option value="playful">Spielerisch</option>
                    <option value="sarcastic">Sarkastisch</option>
                    <option value="sympathetic">Sympathisch</option>
                    <option value="tentative">Vorsichtig</option>
                    <option value="promotional">Werblich</option>
                    <option value="confident">Zuversichtlich</option>
                    <option value="cynical">Zynisch</option>
                </select>

                <select name="nmd_abschnitte_select" id="nmd_abschnitte_select" onchange="headingCount(this.value)">
                    <option value="" disabled selected>Anzahl der Überschriften</option>
                    <option value="1" onclick="headingCount(1)">1</option>
                    <option value="2" onclick="headingCount(2)">2</option>
                    <option value="3" onclick="headingCount(3)">3</option>
                </select>
                <select name="nmd_inhalt_select" id="nmd_inhalt_select">
                    <option value="" disabled selected>Absätze pro Überschriften</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
                <input type="number" name="nmd_words_count" id="nmd_words_count" placeholder="Wörter pro Absatz">


                <button class="button action" onclick="ask_gpt_content_page()">Generieren</button>
            </div>
            <div class="wrap">
                <div class="links container">

                    <div id="keywordTab" class="tab" style="background: grey;" title="Bitte wählen Sie erst die Anzahl der überschriften aus">
                        <div style="display: flex;  align-items: center;" onclick="showTab('keyword', 1)">
                            <h2>Keyword Optimierer</h2>
                            <span id="arrowUp1" style="margin-left: auto; margin-right: 1rem">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                </svg>
                            </span>
                            <span id="arrowDown1" style="margin-left: auto; margin-right: 1rem; display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                </svg>
                            </span>
                        </div>
                        <div id="keywordContainer" style="display: none;">
                            <button id="keywordRechercheButton" style="margin-left: 8px;" class="button action" onclick="get_keywords()">Keyword Recherche mit KI durchführen</button>
                            <div id="keywordsAddContainer">
                                <div class="keywordDiv">
                                    <svg class="removeKeywordDiv" onclick="removeKeywordDiv(this)" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                                    </svg>
                                    <label for="keyword">Keyword:</label>
                                    <br>
                                    <input name="keyword" id="keyword" type="text">
                                    <br>
                                    <label for="keywordAnzahl">Vorkommen im Text:</label>
                                    <br>
                                    <input type="number" name="keywordAnzahl" id="keywordAnzahl" style="width: 8ch;">
                                    <br>
                                    <label for="keywordWhere">Vorkommen in:</label>
                                    <p>Überschrift inkl. Absätze</p>
                                    <div class="flexCenter">
                                        <label class="keywordWhereId1" for="1">1</label>
                                        <input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1">
                                        <label class="keywordWhereId2" for="2">2</label>
                                        <input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2">
                                        <label class="keywordWhereId3" for="3">3</label>
                                        <input type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">

                                    </div>
                                    <br>
                                    <br>
                                    <label for="synonym">Synonyme(optional):</label>
                                    <br>
                                    <input name="synonym" id="synonym" type="text" placeholder="Synonym1, Synonym2, ...">
                                    <br>
                                    <label for="beschreibung">Beschreibung(Optional):</label>
                                    <br>
                                    <input type="text" name="beschreibung" id="beschreibung">
                                    <br>
                                </div>
                            </div>
                            <button class="button button-primary" type="button" onclick="addKeyword()">+ Weiteres Keyword hinzufügen</button>
                            <!-- <button class="button button-primary" style="background-color: #e42a2a;border-color: #e42a2a; width: 50%" type="button" onclick="removeKeyword()">- Keyword Entfernen</button> -->
                        </div>
                    </div>

                    <div id="faqTab" class="tab">
                        <div style="display: flex;  align-items: center;" onclick="showTab('faq', 2)">
                            <h2>FAQ Generator</h2>

                            <span id="arrowUp2" style="margin-left: auto; margin-right: 1rem">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                </svg>
                            </span>
                            <span id="arrowDown2" style="margin-left: auto; margin-right: 1rem; display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                </svg>
                            </span>
                        </div>
                        <div id="faqContainer" style="display: none;">
                            <button class="button button-primary" type="button" onclick="generateFAQ()"> Fragen und Antworten Generieren</button>
                            <div id="faq" class="faq">
                                <label for="question">Frage 1:</label>
                                <br>
                                <input id="question" class="eingabe" name="question" type="text">
                                <br>
                                <label for="answer">Antwort:</label>
                                <br>
                                <input type="text" id="answer" name="answer" class="eingabe">
                            </div>
                            <div class="faqButtons">
                                <button class="button button-primary" type="button" onclick="addFAQ()">+ Weitere Frage hinzufügen</button>

                                <button class="button button-primary" type="button" onclick="generateAnswers()"> Antworten per KI Generieren</button>
                                <br>
                                <!-- <div style="display: flex; align-items: center; ">
            
                                    <input type="checkbox" id="addFAQtoPage" name="addFAQtoPage" value="Bike">
                                    <label for="addFAQtoPage"> FAQ für die Seite verwenden</label>
                                </div> -->

                            </div>
                        </div>
                    </div>
                    <div id="templateTab" class="tab">
                        <div style="display: flex;  align-items: center;" onclick="showTab('template', 3)">
                            <h2>Vorlagen</h2>
                            <span id="arrowUp3" style="margin-left: auto; margin-right: 1rem">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                </svg>
                            </span>
                            <span id="arrowDown3" style="margin-left: auto; margin-right: 1rem; display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                </svg>
                            </span>
                        </div>
                        <div id="templateContainer" class="templateContainer" style="display: none;">

                            <!-- <button class="button button-primary" onclick="template_default()">Templates Zurücksetzen</button> -->
                            <?php
                            $i = 0;
                            $folderCount = 0;
                            $subFolderCount = 0;
                            $currentFolder = "";
                            $currentSubFolder = "";

                            foreach ($testTemplates as $index => $element) {
                                $currentFolder = $index;
                                echo "<div class='folderTab'>";
                                echo "<div class='folderHeaderFlex' onclick='showFolder($folderCount)'>";
                                echo "<h2 style='margin-top: 0px'  >$index</h2>";
                                echo '<span class="editPen" onclick="editFolder(' . "'"  . $currentFolder . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';
                                echo '<span onclick="delete_template_Folder(' . "'"  . $currentFolder . "'"  .  ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                                echo '<span id="folderArrowUp' . $folderCount . '" style=" margin-right: 1rem">';
                                echo ' <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                echo '<path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />';
                                echo '  </svg>';
                                echo ' </span>';
                                echo '<span id="folderArrowDown' . $folderCount . '" style=" margin-right: 1rem; display: none;">';
                                echo '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                echo '<path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />';
                                echo ' </svg>';
                                echo '</span>';
                                echo "</div>";
                                echo "<div id='folderContainer$folderCount' class='folderContainer'>";
                                foreach ($element as $index => $element) {
                                    $currentSubFolder = $index;
                                    echo "<div class='folderTab'>";
                                    echo "<div class='folderHeaderFlex' onclick='showSubFolder("  . $subFolderCount . ")'>";
                                    echo "<h3  class='subFolderHeader'>$index</h3>";
                                    echo '<span class="editPen" onclick="editFolder(' . "'"   . $currentSubFolder  . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';
                                    echo '<span onclick="delete_template_subFolder(' . "'"  . $currentFolder . "','" . $currentSubFolder  . "'"  .  ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                                    echo '<span id="subFolderArrowUp' . $subFolderCount . '" style=" margin-right: 1rem">';
                                    echo ' <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                    echo '<path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />';
                                    echo '  </svg>';
                                    echo ' </span>';
                                    echo '<span id="subFolderArrowDown' . $subFolderCount . '" style=" margin-right: 1rem; display: none;">';
                                    echo '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                    echo '<path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />';
                                    echo ' </svg>';
                                    echo '</span>';
                                    echo "</div>";
                                    echo "<div id='subFolderContainer$subFolderCount' class='folderContainer'>";
                                    foreach ($element as $index => $element) {
                                        echo '<div class="template_card" onclick="get_template(' . "'" . $currentFolder . "'," . "'" . $currentSubFolder . "'," . "'" . $index . "'" . ')">';
                                        echo '<div class="template_left">';
                                        echo '<span title="' . $element[0] . '" style="margin-right:0">' . $index  . '</span>';
                                        echo '<span class="editPen" onclick="editFolder(' . "'"   . $index  . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';

                                        echo '</div>';
                                        echo '<span onclick="delete_template(' . "'" . $currentFolder . "'," . "'" . $currentSubFolder . "'," . "'" . $index . "'" . ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                                        echo '</div>';
                                    }


                                    echo "</div>";
                                    echo "</div>";
                                    $subFolderCount++;
                                }
                                echo "</div>";
                                echo "</div>";
                                $folderCount++;
                            }






                            // foreach ($prompts as $index => $element) {
                            //     echo '<div class="template_card" onclick="get_template(' . "'" . $index . "'" . ')">';
                            //     echo '<div class="template_left">';
                            //     echo '<span title="' . $element[0] . '">' . $index  . '</span>';

                            //     echo '</div>';
                            //     echo '<span onclick="delete_template(' . "'" . $index . "'" . ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                            //     echo '</div>';
                            // }
                            ?>
                            <label for="template_name">Vorlagen Name</label>
                            <textarea name="template_name" id="template_name" cols="30" rows="1"></textarea>
                            <label for="template_description">Vorlagen Beschreibung</label>
                            <textarea name="template_description" id="template_description" cols="30" rows="1"></textarea>
                            <div class="alignMiddle">

                                <label for="unterordner_select">Unterordner: &nbsp;&nbsp;</label>
                                <select name="unterordner_select" id="unterordner_select">

                                    <?php
                                    foreach ($testTemplates as $index => $element) {
                                        $lastFolder = $index;
                                        foreach ($element as $index => $element) {
                                            echo '<option value="' . $index . ',' . $lastFolder . '">' . $lastFolder . ': ' . $index . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <br>
                            <button class="button button-primary" onclick="save_template()">Speichern</button>
                            <div id="folderTab" class="tab subTab" style="margin-bottom: 0;">
                                <div style="display: flex;  align-items: center;" onclick="showTab('folder', 8)">
                                    <h3>Neuen Ordner erstellen</h3>
                                    <span id="arrowUp8" style="margin-left: auto; margin-right: 1rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                        </svg>
                                    </span>
                                    <span id="arrowDown8" style="margin-left: auto; margin-right: 1rem; display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                        </svg>
                                    </span>
                                </div>
                                <div id="folderContainer" style="display: none;">

                                    <label for="folder_name">Ordner Name</label>
                                    <textarea name="folder_name" id="folder_name" cols="30" rows="1"></textarea>
                                    <button class="button button-primary" onclick="createFolder()">Ordner erstellen</button>
                                    <br>
                                </div>


                            </div>

                            <div id="subFolderTab" class="tab subTab">
                                <div style="display: flex;  align-items: center;" onclick="showTab('subFolder', 9)">
                                    <h3>Neuen Unterordner erstellen</h3>
                                    <span id="arrowUp9" style="margin-left: auto; margin-right: 1rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                        </svg>
                                    </span>
                                    <span id="arrowDown9" style="margin-left: auto; margin-right: 1rem; display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                        </svg>
                                    </span>
                                </div>
                                <div id="subFolderContainer" style="display: none;">
                                    <div class="alignMiddle">

                                        <label for="folder_select">Überordner: &nbsp;&nbsp;</label>
                                        <select name="folder_select" id="folder_select">
                                            <?php
                                            foreach ($testTemplates as $index => $element) {
                                                echo '<option value="' . $index . '">' . $index . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <label for="subFolder_name">Unterorder Name</label>
                                    <textarea name="subFolder_name" id="subFolder_name" cols="30" rows="1"></textarea>
                                    <button class="button button-primary" onclick="createSubFolder()">Unterordner erstellen</button>

                                </div>


                            </div>


                        </div>
                    </div>

                </div>
                <div class="mitte container">
                    <div class="mainInputs">
                        <div class="flex_button_header">
                            <div style="display: flex;">
                                <h2>Titel</h2>
                                <img id="infoIconTitle" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                <div id="infoIconTitleText" class="infoTextMini">
                                    Der Titel wird verwendet, um Ihre Seite bzw. Beitrag zu benennen. Aus dem Titel wird von WordPress automatisch die H1-Überschrift, der Seitentitel und die URL generiert. Der Titel hat somit einen Hohen Einfluss auf den Erfolg Ihrer Seite und sollte das Haupt-Keyword gleich zu Beginn enthalten. Zudem wird der Titel in der Beitrags- und Seitenübersicht von WordPress angezeigt. </div>
                            </div>
                        </div>
                        <textarea name="nmd_title_input" id="nmd_title_input" cols="170" rows="1"></textarea>
                        <div class="abschnitte" style="display: none;">
                            <div class='flex_button_header'>
                                <div style="display: flex;">
                                    <h2>Überschriften</h2>
                                    <img id="infoIconUeberschrift" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    <div id="infoIconUeberschriftText" class="infoTextMini">
                                        Der Hauptinhalt bildet den Kern Ihrer Seite und hat einen großen Einfluss auf den Erfolg Ihrer Zielseiten in Suchmaschinen. Nutzen Sie den Keyword-Optimierer, um festzulegen, welche Neben-Keywords Sie innerhalb der Teilüberschriften und Absätze behandeln möchten. Tipps zur optimalen Arbeit mit dem Keyword-Optimierer finden Sie in unserer Anleitung unter dem Menüpunkt „Hilfe und Anleitung“. </div>
                                </div>
                            </div>

                        </div>
                        <textarea name="nmd_abschnitte_input" id="nmd_abschnitte_input" cols="170" rows="10" style="display: none !important"></textarea>
                        <div class="Inhalt">
                            <div class="flex_button_header">
                                <div style="display: flex;">
                                    <h2>Inhalt</h2>
                                    <img id="infoIconInhalt" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    <div id="infoIconInhaltText" class="infoTextMini">
                                        Der Hauptinhalt bildet den Kern Ihrer Seite und hat einen großen Einfluss auf den Erfolg Ihrer Zielseiten in Suchmaschinen. Nutzen Sie den Keyword-Optimierer, um festzulegen, welche Neben-Keywords Sie innerhalb der Teilüberschriften und Absätze behandeln möchten. Tipps zur optimalen Arbeit mit dem Keyword-Optimierer finden Sie in unserer Anleitung unter dem Menüpunkt „Hilfe und Anleitung“. </div>
                                </div>
                            </div>
                            <div style="display: flex;  flex-direction: column;">
                                <div class="checkboxDiv">
                                    <label for="includeInfos" style="font-size: 1rem;">Unternehmensinformationen verwenden</label>
                                    &nbsp;
                                    <input type="checkbox" name="includeInfos" id="includeInfos" style="width: 16px">
                                </div>

                                <div class="checkboxDiv">
                                    <label for="includeShortcode" style="font-size: 1rem;">Kontaktformular einfügen</label>
                                    &nbsp;
                                    <input type="checkbox" name="includeShortcode" id="includeShortcode" style="width: 16px">
                                </div>

                            </div>
                            <br>

                        </div>
                        <textarea name="nmd_inhalt_input" id="nmd_inhalt_input" cols="170" rows="10"></textarea>
                        <div class="Inhalt">
                            <div style="display: flex;">
                                <h2>Meta</h2>
                                <img id="infoIconExcerp" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                <div id="infoIconExcerpText" class="infoTextMini">
                                    Die Vorschau wird als Textauszug innerhalb von Kategorien angezeigt. Die Vorschau hat nur für die Kategorie-Seiten, auf welchen sie angezeigt wird, eine Relevanz und ist für den SEO-Erfolg Ihrer Zielseite nicht von Bedeutung. </div>
                            </div>
                        </div>
                        <textarea name="nmd_excerp_input" id="nmd_excerp_input" cols="170" rows="10"></textarea>
                        <div>
                            <div class="beitragtyp">


                                <h2>Beitragtyp</h2>
                                <select name="nmd_typ_select" id="nmd_typ_select">
                                    <option value="page">Seite</option>
                                    <option value="post">Beitrag</option>
                                </select>

                                <button class="button action" onclick="create_content_page()" style="width: 100%; height: 40px">Seite erzeugen</button>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="rechts container">

                    <div class="prompting">
                        <div class="alignMiddle">
                            <label for="templatePrompt">Templates der SEO-Küche verwenden</label>
                            <input style="margin-left:4px" type="checkbox" name="templatePrompt" id="templatePrompt">
                        </div>
                        <h3>Titel</h3>
                        <textarea name="title_prompt" id="title_prompt" cols="30" rows="10"><?php echo $hardPrompts["titlePrompt"] ?></textarea>
                        <h3>Überschriften</h3>
                        <textarea name="abschnitte_prompt" id="abschnitte_prompt" cols="30" rows="10"><?php echo $hardPrompts["ueberschriftenPrompt"] ?></textarea>
                        <h3>Inhalt</h3>
                        <textarea name="inhalt_prompt" id="inhalt_prompt" cols="30" rows="10"><?php echo $hardPrompts["inhaltPrompt"] ?></textarea>
                        <h3>Meta</h3>
                        <textarea name="excerp_prompt" id="excerp_prompt" cols="30" rows="10"><?php echo $hardPrompts["excerpPrompt"] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hover über Info Text Icon


        document.getElementById('infoIconTitle').addEventListener('mouseover', function() {
            document.getElementById('infoIconTitleText').style.display = 'flex';
        });
        document.getElementById('infoIconTitle').addEventListener('mouseout', function() {
            document.getElementById('infoIconTitleText').style.display = 'none';
        });

        document.getElementById('infoIconUeberschrift').addEventListener('mouseover', function() {
            document.getElementById('infoIconUeberschriftText').style.display = 'flex';
        });
        document.getElementById('infoIconUeberschrift').addEventListener('mouseout', function() {
            document.getElementById('infoIconUeberschriftText').style.display = 'none';
        });

        document.getElementById('infoIconInhalt').addEventListener('mouseover', function() {
            document.getElementById('infoIconInhaltText').style.display = 'flex';
        });
        document.getElementById('infoIconInhalt').addEventListener('mouseout', function() {
            document.getElementById('infoIconInhaltText').style.display = 'none';
        });

        document.getElementById('infoIconExcerp').addEventListener('mouseover', function() {
            document.getElementById('infoIconExcerpText').style.display = 'flex';
        });
        document.getElementById('infoIconExcerp').addEventListener('mouseout', function() {
            document.getElementById('infoIconExcerpText').style.display = 'none';
        });

        // Hover über Info Text Container



        document.getElementById('infoIconTitleText').addEventListener('mouseover', function() {
            document.getElementById('infoIconTitleText').style.display = 'flex';
        });
        document.getElementById('infoIconTitleText').addEventListener('mouseout', function() {
            document.getElementById('infoIconTitleText').style.display = 'none';
        });

        document.getElementById('infoIconUeberschriftText').addEventListener('mouseover', function() {
            document.getElementById('infoIconUeberschriftText').style.display = 'flex';
        });
        document.getElementById('infoIconUeberschriftText').addEventListener('mouseout', function() {
            document.getElementById('infoIconUeberschriftText').style.display = 'none';
        });

        document.getElementById('infoIconInhaltText').addEventListener('mouseover', function() {
            document.getElementById('infoIconInhaltText').style.display = 'flex';
        });
        document.getElementById('infoIconInhaltText').addEventListener('mouseout', function() {
            document.getElementById('infoIconInhaltText').style.display = 'none';
        });

        document.getElementById('infoIconExcerpText').addEventListener('mouseover', function() {
            document.getElementById('infoIconExcerpText').style.display = 'flex';
        });
        document.getElementById('infoIconExcerpText').addEventListener('mouseout', function() {
            document.getElementById('infoIconExcerpText').style.display = 'none';
        });
    </script>

<?php

}
