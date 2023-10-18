<?php
/*
 * Load HTML Views
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Views {
    
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
    }
    
    // Function to safe redirect the page without warnings
    public function redirect( $url ) {
        echo '<script language="javascript">window.location.href="'.$url.'";</script>';
        exit();
    }
    
    /*
     * Function to get the alerts HTML
     * 
     * @param $type string alert type
     * @param $mesage string message
     * 
     * @return $html HTML of alert
     */
    public function load_admin_alerts( $type = 'message', $mesage = '' ) {
        
        $html = $this->load_view( 'admin/alerts/'.$type, [ 'message' => __( $mesage, KIFLAYN_LEARNDASH_TEXT_DOMAIN ) ] );
        
        return $html;
    }
    
    /*
     * Function to load the HTML view
     * 
     * @param $view_name String View name
     * @param $data array of data pass to view
     * 
     * @return $html HTML of the view
     */
    public function load_view( $view_name, $data=array() ) {
        
        //Action before load view
        do_action( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_before_load_view', $view_name, $data );
        
        //Apply filter on $view_name, so that third party plugins and theme can override the view file name        
        $view_name = apply_filters( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_view_to_load', $view_name, $data );
        
        //Apply filter on $data, so that third party plugins and theme can override the $data variable values pass to view
        $data = apply_filters( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'view_data', $data, $view_name );
        
        if( empty($view_name) ) {
            return '';
        }
        
        //Parse $data array into variables
        if( is_array($data) ) {
            extract($data);
        }
        
        $viewPath = KIFLAYN_LEARNDASH_VIEWS_DIR.ltrim($view_name,"/").'.php';
        
        //Allow third party plugins and theme to override the view path
        $viewPath = apply_filters( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_view_path', $viewPath, $view_name, $data );
        
        $html = '';
        
        //Apply filter just before generating the html
        $html = apply_filters( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_before_view_html', $html, $view_name, $data );
        
        //By default send all settings to the template
        $options_instance = Options::get_instance();
        $options = $options_instance->get_plugin_options();
        
        //Generate the HTML
        ob_start();
        require $viewPath;
        //Get HTML
        $html = ob_get_contents();
        ob_end_clean();  
        
        //Do action after html generated
        do_action( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_after_load_view', $html, $view_name, $data );
        
        //Apply filter just before returning the html
        $html = apply_filters( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_view_html', $html, $view_name, $data );
        
        return $html;
    }
}
