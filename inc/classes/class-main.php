<?php

/* 
 * Bootstraps the plugin, this class will load all other classes
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Main {
    
    use Singleton;
    
    //Construct function
    protected function __construct() {
        
        //load class
        $this->setup_hooks();
        
        //Load assets
        Assets::get_instance();
        
        //Load cron
        Cron::get_instance();
        
        //Load views
        Views::get_instance();
        
        //Load options
        Options::get_instance();
        
        //Load Assign Course Class
        Assign_Course::get_instance();
        
        //Load Live Class
        Live_Class::get_instance();
        
        //Create Recordings Class
        Create_Recordings::get_instance();
    }
    
    /*
     * Function to load action and filter hooks
     */
    protected function setup_hooks() {
        
        //actions and filters
        add_action( 'init', [ $this, 'load_textdomain' ] );
        add_action( 'init', [ $this, 'load_thirdparty' ] );
    }
    
    /*
     * Function to include all thirdparty files
     */
    public function load_thirdparty() {
        
        require_once KIFLAYN_LEARNDASH_PLUGIN_PATH.'inc/vimeo/autoload.php';
        
        if( !class_exists( 'ComposerAutoloaderInit518e944dc5fe8dbab3e14646e00c6dd4' ) ) {
            
            require_once KIFLAYN_LEARNDASH_PLUGIN_PATH.'inc/guzzle/vendor/autoload.php';
        }
    }
    
    /**
    * Load plugin textdomain, i.e language directory
    */
    public function load_textdomain() {
        
        load_plugin_textdomain( KIFLAYN_LEARNDASH_TEXT_DOMAIN, false, KIFLAYN_LEARNDASH_LANG_DIR ); 
    }
    
    /*
     * Function that executes once the plugin is activated
     */
    public function kiflayn_learndash_install() {
       
        //Run code once when plugin activated
        if (! wp_next_scheduled ( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_event' )) {            
            wp_schedule_event( time(), KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_interval', KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_event' );
        }  
        
        $db = Db::get_instance();
        $db->create_video_files_table();
        $db->create_zoom_live_url_table();
        $db->create_lessons_recordings_table();
        $db->create_teacher_courses_table();
    }
    
    /*
     * Function that executes once the plugin is deactivated
     */
    public function kiflayn_learndash_uninstall() {
        
        //Run code once when plugin deactivated
        wp_clear_scheduled_hook( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_event' );
    }
}