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
 * Version:           1.0.1
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
define('SEOContent_VERSION', '1.0.1');


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
