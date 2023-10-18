<?php
/*
 * Zoom API
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

use GuzzleHttp\Client;

class Zoom {
    
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
     * Function to create the Zoom meeting
     * 
     * @param $title string Title of the Meeting
     * @param $zoom_user_id Zoom user id
     * 
     * @return $zoom_meeting Zoom meeting object
     */
    public function create_zoom_meeting( $title, $zoom_user_id ) {
        
        $data = array();
        
        $data['topic'] = $title;
        $data['type'] = 3;
        $data['option_host_video'] = false;
        $data['option_participants_video'] = false;
        
        $zoom_meeting = $this->zoom_api_call( '/users/'.$zoom_user_id.'/meetings', 'POST', $data );
        
        return $zoom_meeting;
    }
    
    /*
     * Function to download the video file data
     * 
     * @param $downloadURL string download URL
     * 
     * @return $file Video file
     */
    public function get_download_video_file_data( $downloadURL = '' ) {
        
        $video =  $downloadURL.'?access_token='.$this->get_zoom_oauth_token();
        
        $downloadURL = $video;
        
        $client = new Client([
            'stream'          => false,
            'allow_redirects' => true,
            'cookies'         => true
        ]);

        $response = $client->get($downloadURL);
        
        $file = $response->getBody()->getContents();
        
        return $file;        
    }
    
    /*
     * Function to get the OAuth token
     * 
     * @return $oauth_token string token
     */
    public function get_zoom_oauth_token() {
        
        if ( $oauth_token = get_transient( 'zoom_oauth_token' ) ) {
            
            return $oauth_token;
        }
        
        $options_instance   = Options::get_instance();
        
        $options            = $options_instance->get_plugin_options();
        
        if( empty($options) ) {
            
            return false;
        }
        
        $account_id             = isset($options['zoom_account_id'])?$options['zoom_account_id']:"";// 'JGrawRUfSD6RKH3VqBV09Q';
        $client_id              = isset($options['zoom_client_id'])?$options['zoom_client_id']:"";// '4UgF0sjlQgqxhn3fSHYuWg';
        $client_secret          = isset($options['zoom_client_secret'])?$options['zoom_client_secret']:"";// 'PDObXrfm849FWSM36EEgmCnTYzqBn2VN';
        
        if( empty($account_id) || empty($client_id) || empty($client_secret) ) {
            
            return false;
        }
        
        $url = "https://zoom.us/oauth/token?grant_type=account_credentials&account_id=" . $account_id;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(        
            'grant_type' => 'client_credentials'
        )));

        $headers[] = "Authorization: Basic " . base64_encode($client_id.":".$client_secret);
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        $auth = json_decode($data, true); // token will be with in this json
        
        $oauth_token = isset($auth['access_token'])?$auth['access_token']:"";
        
        set_transient( 'zoom_oauth_token', $oauth_token, 3000 );//local expire in 50 minutes, Zoom will expire the token in 60 minutes
        
        return $oauth_token;
    }
    
    /*
     * Function to call the Zoom API
     * 
     * @param $call string API call
     * @param $method GET/POST/PUT/DELTE methond
     * @param $data array data to pass the pass the API
     * 
     * @return $response mixed response of the API call 
     */
    public function zoom_api_call( $call = '',  $method = 'GET', $data = array() ) { 
        
        if( empty($call) ) {
            
            return false;
        }
        
        $options_instance   = Options::get_instance();
        
        $options            = $options_instance->get_plugin_options();
        
        if( empty($options) ) {
            
            return false;
        }
        
        $token = $this->get_zoom_oauth_token();
        
        if( empty($token) ) {
            
            return false;
        }
        
        $api_url = rtrim( $options['zoom_api_url'], "/" ).$call;
        
        $curl = curl_init();
        
        $curl_data = array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,          
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$token,
                "content-type: application/json"
            ),
        );
    
        if( !empty($data) ) {
            
            $curl_data[CURLOPT_POSTFIELDS] = json_encode( $data );
        }
        
        curl_setopt_array($curl, $curl_data );

        $response = curl_exec($curl);
        
        $err = curl_error($curl);
                
        curl_close($curl);

        $response = json_decode($response, true);
        //print_r($response);
        return $response;      
    }
}