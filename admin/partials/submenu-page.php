<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.seo-kueche.de
 * @since      1.0.0
 *
 * @package    SEOContent
 * @subpackage SEOContent/admin/partials
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/src/scripts/php/create_content_page.php');
nmd_create_content_callback();
