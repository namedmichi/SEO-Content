<?php
function getTokens()
{

    // API URL for the GET request
    $url = 'https://plugin.seo-kueche.de/api/get_tokens';

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return response as a string
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Seo-Header: ' . content_url())); // Set content type to JSON

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
add_action('wp_ajax_get_tokens', 'getTokens');

function gpt_create_post()
{
    try {
        //code...
        $thema = isset($_POST['thema']) ? wp_kses_post($_POST['thema']) : '';
        $title = isset($_POST['title']) ? wp_kses_post($_POST['title']) : '';
        $inhalt  = isset($_POST['inhalt']) ? $_POST['inhalt'] : '';
        $excerpt  = isset($_POST['excerpt']) ? wp_kses_post($_POST['excerpt']) : '';
        $keyword = isset($_POST['keyword']) ? wp_kses_post($_POST['keyword']) : '';
        $typ = isset($_POST['typ']) ? wp_kses_post($_POST['typ']) : '';
        $wordpress_post = array(
            'post_title' => $title,
            'post_content' => $inhalt,
            'post_status' => 'draft',
            'post_author' => 1,
            'post_type' => $typ,
            'post_excerpt' => $excerpt,
        );
        $typeSave = $typ;
        echo $typeSave;
        $id = wp_insert_post($wordpress_post);
        save_page_meta_description($id, $excerpt);
        save_page_keyword($id, $keyword);
        if ($typ == "page") {
            echo "The variable \$typ is 'page'.";
            echo $thema;
            $updated_post = array(
                'ID'        => $id,
                'post_name' => $thema,
            );
            wp_update_post($updated_post);
        } elseif ($typeSave == "post") {
            echo "The variable \$typ is 'post'.";
        } else {
            echo "The variable \$typ is neither 'page' nor 'post'.";
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



function save_page_keyword($page_id, $keyword)
{


    // Perform database operations to save the meta description for a specific page.
    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_keywords';

    $wpdb->insert(
        $table_name,
        array(
            'page_id' => $page_id,
            'keyword' => $keyword
        )
    );
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);

    $countArray = get_counts($jsonData);

    $unterOrdnerCount = $countArray[0] + 1;
    $promptCount = $countArray[1] + 1;

    $jsonData[$name] = [];
    $jsonData[$name]["Unterordner " . $unterOrdnerCount] = [];
    $jsonData[$name]["Unterordner " . $unterOrdnerCount]["Prompt " . $promptCount] = ["", "", "", "", "", "", "", ""];

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    echo file_put_contents($path, $jsonData);
}
function get_counts($array)
{
    $unterOrdnerCount = 0;
    $promptCount = 0;

    foreach ($array as $ordner) {
        if (is_array($ordner)) {
            foreach ($ordner as $unterOrdner) {
                $unterOrdnerCount++;
                if (is_array($unterOrdner)) {
                    foreach ($unterOrdner as $prompts) {
                        if (is_array($prompts)) {
                            $promptCount++;
                        }
                    }
                }
            }
        }
    }

    return [$unterOrdnerCount, $promptCount];
}
function edit_folder()
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

    function changeKey($array, $oldKey, $newKey)
    {
        // Check if the old key exists
        if (array_key_exists($oldKey, $array)) {
            // Change the key
            $keys = array_keys($array);
            $keys[array_search($oldKey, $keys)] = $newKey;

            // Reassemble the array
            $array = array_combine($keys, array_values($array));
            return $array;
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
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

        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '\wp-load.php');
    } catch (\Throwable $th) {
        try {
            //code...
            require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))))) . '\wp-load.php');
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    $path = content_url() . '/plugins/SEOContent/src/scripts/php/templateTest.json';
    $jsonString = file_get_contents($path);
    echo $jsonString;
}

function get_keyword_api()
{

    $topic = isset($_POST['topic']) ? $_POST['topic'] : '';

    $url = 'https://plugin.seo-kueche.de/api/getKeyword';

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Seo-Header: ' . content_url()));

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

    $url = 'https://plugin.seo-kueche.de/api/get_best_keyword';

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Seo-Header: ' . content_url()));

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

function askGPTOld()
{
    $chat = isset($_POST['chat']) ? $_POST['chat'] : '';
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '';
    // API URL for the POST request
    $url = 'https://plugin.seo-kueche.de/api/chat';

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

function askGPT()
{
    // Your current setup and parameters
    $chat = isset($_POST['chat']) ? $_POST['chat'] : '';
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '';
    $url = 'https://plugin.seo-kueche.de/api/chat'; // Assuming the URL might be different for starting the task

    // Data preparation remains the same
    $data = array(
        'messages' => $chat,
        'model' => $model,
        'temperature' => $temperature
    );
    $jsonData = json_encode($data);

    // The rest of the cURL setup remains the same
    $ch = curl_init($url);
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST1
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set POST data
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);  // Sets a timeout of 600 seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Seo-Header: ' . content_url())); // Set content type to JSON

    $response = curl_exec($ch);

    // Error handling remains the same
    if (curl_errno($ch)) {
        wp_send_json_error('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Assuming the response contains an identifier for the task
    $response_data = json_decode($response, true);
    var_dump($response_data);



    // Return a response to indicate the task has started
    echo 'Task started.';
    wp_die();
}


function check_task_status()
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
        echo $response_data['answer'];
    } else {
        var_dump($response_data);
        // If the task is not yet complete, indicate so
        echo 'Task still processing.';
    }
    wp_die();
}
add_action('wp_ajax_check_task_status', 'check_task_status');




add_action('wp_ajax_ask_gpt', 'askGPT');
add_action('wp_ajax_gpt_create_post', 'gpt_create_post');
add_action('wp_ajax_my_ajax_request2', 'gpt_add_script_to_post');
add_action('wp_ajax_delete_template', 'delete_template');
add_action('wp_ajax_create_folder', 'create_folder');
add_action('wp_ajax_create_sub_folder', 'create_sub_folder');
add_action('wp_ajax_save_template', 'save_template');
add_action('wp_ajax_get_template', 'get_prompt_template');
add_action('wp_ajax_edit_folder', 'edit_folder');
add_action('wp_ajax_edit_sub_folder', 'edit_sub_folder');
add_action('wp_ajax_get_keyword_api', 'get_keyword_api');
add_action('wp_ajax_get_best_keyword_api', 'get_best_keyword_api');
