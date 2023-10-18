<?php
/*
 * Enqueue plugin assets
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Assets {
    
    use Singleton;
    
    //Construct function
    protected function __construct() {
        
        //load class
        $this->setup_hooks();
    }
    
    /*
     * Function to load action and filter hooks
     */
    protected function setup_hooks() {
        
        //actions and filters   
             
        //Register styles
        add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
        
        //Register styles for the admin
        if( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register_styles' ] );
        }
        
        //Register scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] ); 
        
        //Register scripts for the admin
        if( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
        }
    }
    
    /*
     * Function to register styles
     */
    public function register_styles() {
        
        //Register style
        wp_register_style( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_style', KIFLAYN_LEARNDASH_ASSETS_DIR_URL.'css/style.css', [], filemtime(  KIFLAYN_LEARNDASH_ASSETS_DIR_PATH. 'css/style.css' ) );
        
        //enqueue style
        wp_enqueue_style( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_style' );
    }
    
    /*
     * Function to register scripts 
     */
    public function register_scripts() {
        
        //Register script
        wp_register_script( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_js', KIFLAYN_LEARNDASH_ASSETS_DIR_URL. 'javascript/main.js', ['jquery'], filemtime( KIFLAYN_LEARNDASH_ASSETS_DIR_PATH . 'javascript/main.js' ), true );
        
        wp_enqueue_script( 'jquery' );
        
        //enqueue script
        wp_enqueue_script( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_js' );
        
        //localize a registered script with data for a JavaScript variable
        wp_localize_script( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_js', KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_data', array(
                            'ajaxurl'   => admin_url( 'admin-ajax.php' )
                            ));
    }
}