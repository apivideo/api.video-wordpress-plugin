<?php
/*
 * Plugin Name: api.video
 * Plugin URI: https://api.video/
 * Description: <a target="_blank" href="https://api.video">api.video</a> provides video infrastructure for product builders. Use <a target="_blank" href="https://api.video">api.video</a> lightning-fast video APIs for integrating, scaling, and managing on-demand & low latency live streaming features in your WordPress site.
 * Version: 1.0
 * Author: VVM Agency
 * Author URI: https://vvm.agency/
 * 
 * 
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: the text domain of the plugin
 * Domain Path: where to find the translation files (see How to Internationalize Your Plugin)
 */
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
// define( 'APIVIDEO_ROOT_URL', dirname( __DIR__ ) . '/' );
define ('APIVIDEO_ROOT_URL', plugin_dir_path(__FILE__));
require_once APIVIDEO_ROOT_URL . 'includes/av_functions.php';
require_once APIVIDEO_ROOT_URL . 'includes/av_pages.php'; 
require_once APIVIDEO_ROOT_URL . 'includes/av_page_add_new_video.php'; 
require_once APIVIDEO_ROOT_URL . 'includes/av_page_library.php'; 
require_once APIVIDEO_ROOT_URL . 'includes/av_page_settings.php'; 
?>
