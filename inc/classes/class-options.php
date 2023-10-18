<?php
/*
 * Class to create theme options page
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Options {
    
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
        add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
    }
    
    /**
    * Returns all plugin options
    *
    * @since 1.0.0
    */
    public function get_plugin_options() {
        
        $options = get_option( KIFLAYN_LEARNDASH_SETTINGS_KEY );
        
        if( empty( $options ) || !is_array( $options ) ) {
            $options = [];
        }
        
        return $options;        
    }

                
   /**
    * Returns single plugin option
    *
    * @since 1.0.0
    */
    public function get_plugin_option( $id ) {
       
        $options = $this->get_plugin_options();
        
        if ( isset( $options[$id] ) ) {
            
            return $options[$id];
        }
    }
    
    /*
     * Function to update all options values
     * 
     * @param $options mixed value of the options
     */
    public function update_plugin_options( $options ) {
        
        update_option( KIFLAYN_LEARNDASH_SETTINGS_KEY, $options );
    }
    
    /*
     * Function to update any single option value
     * 
     * @param $id string of the option that need to update
     * @param $val mixed value of the option
     */
    public function update_plugin_option( $id, $val ) {
        
        $options = $this->get_plugin_options();
        
        $options[$id] = $val;
        
        update_option( KIFLAYN_LEARNDASH_SETTINGS_KEY, $options );
    }
    
    /**
    * Add sub menu page
    *
    * @since 1.0.0
    */
    public function add_plugin_admin_menu() {
        
        add_menu_page(
            esc_html__( KIFLAYN_LEARNDASH_PLUGIN_NAME, KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            esc_html__( KIFLAYN_LEARNDASH_PLUGIN_NAME, KIFLAYN_LEARNDASH_TEXT_DOMAIN ),   
            'manage_options',
            'kiflayn_learndash_settings',
             [ $this, 'settings_admin_page' ]
        );
        
        add_submenu_page( 'kiflayn_learndash_settings', 
            esc_html__( 'Settings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            esc_html__( 'Settings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            'manage_options',
            'kiflayn_learndash_settings',
            [ $this, 'settings_admin_page' ] 
        );
    }

    /**
     * Settings page output
     *
     * @since 1.0.0
     */
    public function settings_admin_page() {
        
        $message = '';
        
        $views = Views::get_instance();
        
        $options = $this->get_plugin_options(); 
        
        if( isset($_POST['btnsave']) && $_POST['btnsave'] != "" ) { 
            
            $exclude = array('btnsave');
            
            foreach( $_POST as $k => $v ) {
                
                if( !in_array( $k, $exclude )) {
                    
                    if(!is_array($v)) {
                        $val = sanitize_text_field($v);
                    }else{
                        $val = $v;
                    }
                    
                    $options[$k] = $val;
                }
            }            
            
            $zoom = Zoom::get_instance();
            
            $zoom_users = $zoom->zoom_api_call( '/users?status=active&page_size=300&page_number=0' );
            
            $options['zoom_users'] = $zoom_users['users']; 
        
            $this->update_plugin_options( $options );
            
            $message = $views->load_admin_alerts( 'message', 'Settings Saved Successfully!' );
        }
        
        //echo '<pre>';print_r($options['zoom_users']);echo '</pre>';
        
        $html = $views->load_view( 'admin/settings', ['message' => $message] );
        
        echo $html;
    }
}