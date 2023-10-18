<?php
/*
 * Viemo Upload API
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class VimeoUpload {
    
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
    
    /*
     * Function to upload video on Vimeo
     * 
     * @param $video_link String video url
     * @param $name String video title
     * 
     * @return $video_response mixed upload video response
     */
    public function upload_video( $video_link, $name = '' ) {
        
        $options_instance = Options::get_instance();
        
        $options = $options_instance->get_plugin_options();
        
        $vimeo = new \Vimeo\Vimeo( $options['vimeo_client_id'], $options['vimeo_client_secret'] );
        
        $vimeo->setToken( $options['vimeo_access_token'] );
        
        $video_response = $vimeo->request( '/me/videos', array( 'name' =>$name, 'type' => 'pull', 'link' => $video_link ), 'POST' ); 
        
        $rename_response = $vimeo->request( $video_response['body']['uri'], array( 'name' => $name ), 'PATCH' );
        
        return $video_response;
    }
}