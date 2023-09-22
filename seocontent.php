<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.seo-kueche.de
 * @since             1.0.0
 * @package           SEOContent
 *
 * @wordpress-plugin
 * Plugin Name:       SEO Content
 * Plugin URI:        https://www.seo-kueche.de
 * Description:       KI-gestütztes Plugin zur automatisierten Erstellung von SEO-konformen Inhalten und Meta-Daten.
 * Version:           1.0.3
 * Author:            SEO Küche
 * Author URI:        https://www.seo-kueche.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       seocontent
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
define('__ROOT__', dirname(dirname(__FILE__)));




require_once __DIR__ . '/src/scripts/php/plugin_mask.php';
require_once __DIR__ . '/src/scripts/php/create_content_page.php';
require_once __DIR__ . '/src/scripts/php/create_image_page.php';




/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SEOContent_VERSION', '1.0.2');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-seocontent-activator.php
 */
function activate_SEOContent()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-seocontent-activator.php';
	SEOContent_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-seocontent-deactivator.php
 */
function deactivate_SEOContent()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-seocontent-deactivator.php';
	SEOContent_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_SEOContent');
register_deactivation_hook(__FILE__, 'deactivate_SEOContent');



function create_script_table()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'seocontent_faqs';

	$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id mediumint(9) NOT NULL,
        script_content text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}


register_activation_hook(__FILE__, 'check_and_create_table');

//Creates the table for the FAQ when activating the plugin if it doesn't exist
function check_and_create_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'seocontent_faqs';

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		// Table not in database. Create new table.
		create_script_table();
	}
}


register_activation_hook(__FILE__, 'set_settings_option');

function set_settings_option()
{
	// Überprüfen, ob die Option bereits existiert
	$json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/settings.json';
	if (!get_option('seocontent_settings')) {
		// Die Option existiert nicht, daher erstellen wir sie aus einer JSON-Datei

		// Pfad zur JSON-Datei in deinem Plugin-Verzeichnis

		// Lese den Inhalt der JSON-Datei
		$json_data = file_get_contents($json_file_path);

		// Konvertiere JSON in ein PHP-Array
		$settings = json_decode($json_data, true);

		// Füge die Option "seocontent_settings" mit den JSON-Daten hinzu
		add_option('seocontent_settings', $settings);
	} else {
		// Die Option existiert, lade die Option und speichere sie in der JSON-Datei

		$seocontent_settings = get_option('seocontent_settings');

		// Konvertiere die Option in JSON-Format
		$json_data = json_encode($seocontent_settings, JSON_PRETTY_PRINT);

		// Speichere die JSON-Daten in der Datei
		file_put_contents($json_file_path, $json_data);
	}
}
register_activation_hook(__FILE__, 'set_variables_option');

function set_variables_option()
{
	// Überprüfen, ob die Option bereits existiert
	$json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/variables.json';
	if (!get_option('seocontent_variables')) {
		// Die Option existiert nicht, daher erstellen wir sie aus einer JSON-Datei

		// Pfad zur JSON-Datei in deinem Plugin-Verzeichnis

		// Lese den Inhalt der JSON-Datei
		$json_data = file_get_contents($json_file_path);

		// Konvertiere JSON in ein PHP-Array
		$settings = json_decode($json_data, true);

		// Füge die Option "seocontent_settings" mit den JSON-Daten hinzu
		add_option('seocontent_variables', $settings);
	} else {
		// Die Option existiert, lade die Option und speichere sie in der JSON-Datei

		$seocontent_settings = get_option('seocontent_variables');

		// Konvertiere die Option in JSON-Format
		$json_data = json_encode($seocontent_settings, JSON_PRETTY_PRINT);

		// Speichere die JSON-Daten in der Datei
		file_put_contents($json_file_path, $json_data);
	}
}


register_activation_hook(__FILE__, 'set_template_option');


function set_template_option()
{
	// Überprüfen, ob die Option bereits existiert
	$json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/templateTest.json';
	if (!get_option('seocontent_templates')) {
		// Die Option existiert nicht, daher erstellen wir sie aus einer JSON-Datei

		// Pfad zur JSON-Datei in deinem Plugin-Verzeichnis

		// Lese den Inhalt der JSON-Datei
		$json_data = file_get_contents($json_file_path);

		// Konvertiere JSON in ein PHP-Array
		$settings = json_decode($json_data, true);

		// Füge die Option "seocontent_settings" mit den JSON-Daten hinzu
		add_option('seocontent_templates', $settings);
	} else {
		// Die Option existiert, lade die Option und speichere sie in der JSON-Datei

		$seocontent_templates = get_option('seocontent_templates');

		// Konvertiere die Option in JSON-Format
		$json_data = json_encode($seocontent_templates, JSON_PRETTY_PRINT);

		// Speichere die JSON-Daten in der Datei
		file_put_contents($json_file_path, $json_data);
	}
}

// Definiere eine benutzerdefinierte Hook
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

// Füge deine benutzerdefinierte Hook zu WordPress hinzu
add_action('update_seocontent_settings_hook', 'update_seocontent_settings');



// Definiere eine benutzerdefinierte Hook
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

// Füge deine benutzerdefinierte Hook zu WordPress hinzu
add_action('update_seocontent_templates_hook', 'update_seocontent_template');


// Definiere eine benutzerdefinierte Hook
function update_seocontent_variables()
{
	$json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/variables.json';

	$json_data = file_get_contents($json_file_path);

	$settings = json_decode($json_data, true);

	update_option('seocontent_variables', $settings);
}


add_action('update_seocontent_variables_hook', 'update_seocontent_variables');


function import_seocontent_template()
{
	$templates  = isset($_POST['templates']) ? $_POST['templates'] : '';

	$json_file_path = plugin_dir_path(__FILE__) . 'src/scripts/php/templateTest.json';


	file_put_contents($json_file_path, $templates);

	update_option('seocontent_templates', $templates);
}

// Füge deine benutzerdefinierte Hook zu WordPress hinzu
add_action('import_seocontent_templates_hook', 'import_seocontent_template');

add_action('wp_ajax_import_seocontent_settings_action', 'import_seocontent_template_action');
add_action('wp_ajax_nopriv_import_seocontent_template_action', 'import_seocontent_template_action'); // Für nicht angemeldete Benutzer

function import_seocontent_template_action()
{
	// Führe die benutzerdefinierte Hook aus
	do_action('import_seocontent_templates_hook');

	wp_die(); // Beende die AJAX-Anfrage
}

add_action('wp_ajax_update_seocontent_settings_action', 'update_seocontent_settings_action');
add_action('wp_ajax_nopriv_update_seocontent_settings_action', 'update_seocontent_settings_action'); // Für nicht angemeldete Benutzer

function update_seocontent_settings_action()
{
	// Führe die benutzerdefinierte Hook aus
	do_action('update_seocontent_settings_hook');

	wp_die(); // Beende die AJAX-Anfrage
}


add_action('wp_ajax_update_seocontent_templates_action', 'update_seocontent_templates_action');
add_action('wp_ajax_nopriv_update_seocontent_templates_action', 'update_seocontent_templates_action'); // Für nicht angemeldete Benutzer

function update_seocontent_templates_action()
{
	// Führe die benutzerdefinierte Hook aus
	do_action('update_seocontent_templates_hook');

	wp_die(); // Beende die AJAX-Anfrage
}
add_action('wp_ajax_update_seocontent_variables_action', 'update_seocontent_variables_action');
add_action('wp_ajax_nopriv_update_seocontent_variables_action', 'update_seocontent_variables_action'); // Für nicht angemeldete Benutzer

function update_seocontent_variables_action()
{
	// Führe die benutzerdefinierte Hook aus
	do_action('update_seocontent_variables_hook');

	wp_die(); // Beende die AJAX-Anfrage
}

// Adds the FAQ from "Text erstellen" into the footer

function insert_page_script()
{
	if (is_page() || is_single()) {
		global $post;
		global $wpdb;

		$table_name = $wpdb->prefix . 'seocontent_faqs';
		$result = $wpdb->get_row("SELECT * FROM $table_name WHERE page_id = $post->ID");

		if ($result) {
			echo '<script type="application/ld+json">' . $result->script_content . '</script>';
		}
	}
}

// Hook the function into wp_footer
add_action('wp_footer', 'insert_page_script');





register_activation_hook(__FILE__, 'check_and_create_seo_table');

//Creates the table for the meta description when activating the plugin if it doesn't exist
function check_and_create_seo_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'seocontent_metas';

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		// Table not in database. Create new table.
		create_seo_table();
	}
}

//Creates the table for the meta description
function create_seo_table()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'seocontent_metas';

	$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id mediumint(9) NOT NULL,
        meta_description text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

// Adds the meta description into the header
function insert_page_meta_description()
{
	if (is_page() || is_single()) {
		global $post;
		global $wpdb;

		$table_name = $wpdb->prefix . 'seocontent_metas';
		$result = $wpdb->get_row("SELECT * FROM $table_name WHERE page_id = $post->ID");

		if ($result) {
			echo '<meta id="seocontent" name="description" content="' . esc_attr($result->meta_description) . '">';
		}
	}
}

// Hook the function into wp_head
add_action('wp_head', 'insert_page_meta_description');



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-seocontent.php';
wp_register_style('my_plugin_name_dashicons', '/wp-content/plugins/SEOContent/css/seologo.css');
wp_enqueue_style('my_plugin_name_dashicons');
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */


add_action('admin_enqueue_scripts', 'nmd_admin_enqueue_scripts');

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function nmd_admin_enqueue_scripts()
{
	wp_enqueue_style('nmd-style', plugin_dir_url(__FILE__) . 'build/index.css');
	wp_enqueue_script('nmd-script', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-element'), '1.0.0', true);
}


// KüchenGPT KI Block
function nmd_registe_block()
{
	register_block_type(__DIR__ . "/src/blocks/");
}
add_action('init', 'nmd_registe_block');


add_filter('plugin_action_links', 'misha_settings_link', 10, 2);

function misha_settings_link($links_array, $plugin_file_name)
{
	// $plugin_file_name is plugin-folder/plugin-name.php


	if (strpos($plugin_file_name, basename(__FILE__))) {
		// we can add one more array element at the beginning with array_unshift()
		$links_array[] = '<a href="https://www.seo-kueche.de/ratgeber/">Ratgeber</a>';
		$links_array[] = '<a href="https://www.seo-kueche.de/kontakt/">Support</a>';
		$links_array[] = '<a href="admin.php?page=seo_content.php">Einstellungen</a>';
		$links_array[] = '<a href="admin.php?page=seohelp.php">Hilfe</a>';
	}

	return $links_array;
}
function run_SEOContent()
{
	$plugin = new SEOContent();
	$plugin->run();
}

run_SEOContent();
wp_localize_script('mylib', 'WPURLS', array('siteurl' => get_option('siteurl')));
