<?php
/*
 * Zapier Webhook function
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Zapier {
    
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
     * Function to call Zapier Webhook
     * 
     * @param $url String URL of Zapier Webhook
     * @param $data Array of Data that need to pass
     * 
     * @return $response Zapier webhook response
     */
    public function zapier_webhook( $url, $data ) {
        
        if( empty($data) || empty($url) ) {
            return false;
        }
        
        // stuff it into a query
        $zap_query = http_build_query( $data );
        $url = $url.'?'.$zap_query;
        
        // curl my data into the zap
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $response = curl_exec( $ch );
        
        return $response;
    }
}