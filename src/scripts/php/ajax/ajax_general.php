<?php






add_action('wp_ajax_save_variable_settings', 'save_variable_settings');
function save_variable_settings()
{

    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/variables.json';

    $variables = isset($_POST['variables']) ? $_POST['variables'] : '';
    $jsonData = json_encode($variables, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);
    wp_die();
}


add_action('wp_ajax_save_main_settings', 'save_main_settings');
function save_main_settings()
{
    $path = ABSPATH . 'wp-content/plugins/SEOContent/src/scripts/php/settings.json';
    $apiKey = isset($_POST['apiKey']) ? $_POST['apiKey'] : '';
    $firmenname = isset($_POST['firmenname']) ? $_POST['firmenname'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
    $Gewerbe = isset($_POST['Gewerbe']) ? $_POST['Gewerbe'] : '';
    $whyUs = isset($_POST['whyUs']) ? $_POST['whyUs'] : '';
    $kontaktSeite = isset($_POST['kontaktSeite']) ? $_POST['kontaktSeite'] : '';
    $usps = isset($_POST['usps']) ? $_POST['usps'] : '';
    $cta = isset($_POST['cta']) ? $_POST['cta'] : '';
    $shortcode = isset($_POST['shortcode']) ? $_POST['shortcode'] : '';
    $settingsArray = array('apiKey' => $apiKey, 'firmenname' => $firmenname, 'adresse' => $adresse, 'Gewerbe' => $Gewerbe, 'warumWir' => $whyUs, 'usps' => $usps, 'kontaktSeite' => $kontaktSeite, 'cta' => $cta, 'shortcode' => $shortcode);
    $jsonData = json_encode($settingsArray, JSON_PRETTY_PRINT);
    file_put_contents($path, $jsonData);
    wp_die();
}


add_action('wp_ajax_get_main_settings', 'get_main_settings');
function get_main_settings()
{
    $path = content_url() . '/plugins/SEOContent/src/scripts/php/settings.json';
    $jsonString = file_get_contents($path);
    echo $jsonString;
    wp_die();
}



add_action('wp_ajax_get_page_keyword', 'get_page_keyword');
function get_page_keyword()
{
    $page_id = isset($_POST['pageId']) ? $_POST['pageId'] : '';

    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_keywords';

    $result = $wpdb->get_row("SELECT * FROM $table_name WHERE page_id = $page_id");

    echo json_encode($result);

    wp_die();
}

add_action('wp_ajax_update_page_keyword', 'update_page_keyword');
function update_page_keyword()
{
    $page_id = isset($_POST['page_id']) ? $_POST['page_id'] : '';
    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

    global $wpdb;
    $table_name = $wpdb->prefix . 'seocontent_keywords';


    // Check if the record exists
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE page_id = %d",
        $page_id
    ));

    if ($exists) {
        // Record exists, update it
        $wpdb->update(
            $table_name,
            array('keyword' => $keyword),
            array('page_id' => $page_id)
        );
    } else {
        // Record does not exist, create a new one
        $wpdb->insert(
            $table_name,
            array(
                'page_id' => $page_id,
                'keyword' => $keyword
            )
        );
    }

    wp_die();
}



add_action('wp_ajax_update_seocontent_settings_action', 'update_seocontent_settings');
function update_seocontent_settings()
{
    // Pfad zur JSON-Datei in deinem Plugin-Verzeichnis
    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/settings.json';

    // Lese den Inhalt der JSON-Datei
    $json_data = file_get_contents($json_file_path);

    // Konvertiere JSON in ein PHP-Array
    $settings = json_decode($json_data, true);

    // Aktualisiere die Option "seocontent_settings" mit den neuen JSON-Daten
    update_option('seocontent_settings', $settings);
}




add_action('wp_ajax_update_seocontent_templates_action', 'update_seocontent_template');
function update_seocontent_template()
{
    // Pfad zur JSON-Datei in deinem Plugin-Verzeichnis
    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/templateTest.json';

    // Lese den Inhalt der JSON-Datei
    $json_data = file_get_contents($json_file_path);

    // Konvertiere JSON in ein PHP-Array
    $settings = json_decode($json_data, true);

    // Aktualisiere die Option "seocontent_settings" mit den neuen JSON-Daten
    update_option('seocontent_templates', $settings);
}





add_action('wp_ajax_update_seocontent_variables_action', 'update_seocontent_variables');
function update_seocontent_variables()
{
    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/variables.json';

    $json_data = file_get_contents($json_file_path);

    $settings = json_decode($json_data, true);

    update_option('seocontent_variables', $settings);
}




add_action('wp_ajax_import_seocontent_template_action', 'import_seocontent_template');
function import_seocontent_template()
{

    $templates  = isset($_POST['templates']) ? $_POST['templates'] : '';

    $json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/templateTest.json';

    $jsonData = json_encode($templates, JSON_PRETTY_PRINT);
    file_put_contents($json_file_path, $jsonData);

    update_option('seocontent_templates', $templates);
    wp_die();
}
