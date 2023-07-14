<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.seo-kueche.de
 * @since      1.0.0
 *
 * @package    SEOContent
 * @subpackage SEOContent/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SEOContent
 * @subpackage SEOContent/admin
 * @author     SEOKüche <info@seo-kueche.de>
 */



add_action('wp_before_admin_bar_render', 'image_admin_bar');
function image_admin_bar()
{
	global $wp_admin_bar;
	$wp_admin_bar->add_menu(array(
		'id' => 'nmd_create_image_admin_menu',
		'parent' => false,
		'title' => '<a href="' . admin_url('admin.php?page=seo_content.php') . '">  <img style="width:33px !important;" class="nmdIcon" src="' . content_url() . '/plugins/SEOContent/src/assets/seologo.png"> <p>SEO Content</p> </a> ',
		'href' => "",
		'meta' => array(
			'onclick' => '',
			'html' => '',
			'class' => 'has-icon nmdAdminMenuIcon',
			'target' => '',
			'title' => ''
		)
	));
}


class SEOContent_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SEOContent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SEOContent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/seocontent-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SEOContent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SEOContent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/seocontent-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Fügt Admin menu hinzu
	 *
	 * @since    1.0.0
	 */

	public function my_admin_menu()
	{
		add_menu_page('Seo Content', 'SEO Content', 'edit_posts', 'seo_content.php', array($this, 'myplugin_admin_menu'),  content_url() . "/plugins/SEOContent/src/assets/favicon-32x32.png", 250);
		add_submenu_page('seo_content.php', 'Sub Level', 'Texte erstellen', 'edit_posts', 'content.php', array($this, 'myplugin_admin_sub_page'));
		add_submenu_page('seo_content.php', 'Sub Level', 'Bilder erstellen', 'edit_posts', 'image.php', array($this, 'myplugin_admin_sub_page_image'));
		add_submenu_page('seo_content.php', 'Sub Level', 'Titel und Meta', 'edit_posts', 'title_meta.php', array($this, 'myplugin_admin_sub_page_title_and_meta'));
		//add_submenu_page('seo_content.php', 'Sub Level', 'Hilfe', 'edit_posts', 'seohelp.php', array($this, 'myplugin_admin_sub_page_help'));
		// add_submenu_page('seo_content.php', 'Sub Level', 'Test', 'SEOContent', 'testtest.php', array($this, 'myplugin_admin_sub_page_test'));
	}

	public function myplugin_admin_menu()
	{

		require_once 'partials/seocontent-admin-display.php';
	}
	public function myplugin_admin_sub_page()
	{
		require_once 'partials/submenu-page.php';
	}
	public function myplugin_admin_sub_page_image()
	{
		require_once 'partials/submenu-page-image.php';
	}
	public function myplugin_admin_sub_page_title_and_meta()
	{
		require_once 'partials/submenu-page-title-and-meta.php';
	}
	public function myplugin_admin_sub_page_help()
	{
		require_once 'partials/submenu-page-help.php';
	}
	// public function myplugin_admin_sub_page_test()
	// {
	// 	require_once 'partials/test.php';
	// }
}
