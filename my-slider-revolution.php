<?php
/**
 * Plugin Name: My Slider Revolution
 * Description: WordPress用のスライダープラグイン
 * Version: 0.1.0
 * Author: Osamu Moshio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin constants
 */
define( 'MSR_VERSION', '0.1.0' );
define( 'MSR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MSR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Slider post type
 */
require_once MSR_PATH . 'includes/class-slider-post-type.php';

/**
 * Slider meta box
 */
require_once MSR_PATH . 'includes/class-slider-meta-box.php';