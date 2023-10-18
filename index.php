<?php
/*
Plugin Name: Kiflayn LearnDash
Description: Kiflayn LearnDash
Version: 1.0.0
Author: Muhammad Atiq
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

//ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

//Global define variables
define( 'KIFLAYN_LEARNDASH_PLUGIN_NAME', 'Kiflayn LearnDash' );
define( 'KIFLAYN_LEARNDASH_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'KIFLAYN_LEARNDASH_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'KIFLAYN_LEARNDASH_SITE_BASE_URL',  rtrim(get_bloginfo('url'),"/")."/");
define( 'KIFLAYN_LEARNDASH_LANG_DIR', KIFLAYN_LEARNDASH_PLUGIN_PATH.'language/' );
define( 'KIFLAYN_LEARNDASH_VIEWS_DIR', KIFLAYN_LEARNDASH_PLUGIN_PATH.'views/' );
define( 'KIFLAYN_LEARNDASH_ASSETS_DIR_URL', KIFLAYN_LEARNDASH_PLUGIN_URL.'assets/' );
define( 'KIFLAYN_LEARNDASH_ASSETS_DIR_PATH', KIFLAYN_LEARNDASH_PLUGIN_PATH.'assets/' );
define( 'KIFLAYN_LEARNDASH_SETTINGS_KEY', '_kiflayn_learndash_options' );
define( 'KIFLAYN_LEARNDASH_TEXT_DOMAIN', 'kiflayn_learndash' );

//Load the classes
require_once KIFLAYN_LEARNDASH_PLUGIN_PATH.'/inc/helpers/autoloader.php';
        
//Get main class instance
$main = KIFLAYN_LEARNDASH\Inc\Main::get_instance();

//Plugin activation hook
register_activation_hook( __FILE__, [ $main, 'kiflayn_learndash_install' ] );

//Plugin deactivation hook
register_deactivation_hook( __FILE__, [ $main, 'kiflayn_learndash_uninstall' ] );