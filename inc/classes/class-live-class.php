<?php
/*
 * Create Join Live Class URL
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Live_Class {
    
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
        add_action( 'admin_menu', [ $this, 'add_live_class_menu' ] );
        
        add_shortcode( 'kiflayn_learndash_join_class_url', [ $this, 'join_class_url_shortcode' ] );
        
        add_filter( 'the_content', [ $this, 'content_filter' ] );
        
        add_action( 'wp_ajax_kiflayn_learndash_load_students', [ $this, 'get_students_select_box' ] );
    }
    
    /*
     * Shortcode to show the Join Live Class button 
     */
    public function join_class_url_shortcode() {
        
        $html = '';
        //ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
        global $post;
        
        if( $post->post_type == 'sfwd-courses' ) {
            
            $current_user = wp_get_current_user();

            if ( 0 == $current_user->ID ) {
                return $html;
            } else {                
                $db         = Db::get_instance();                
                $allowed_roles = [ 'administrator' ];                
                if( array_intersect( $allowed_roles, $current_user->roles ) ) {    
                    $join_class_url = $db->get_data( $db->zoom_live_url_table, "course_id = '".$post->ID."'", false, "DISTINCT url" ); 
                }else{
                    $join_class_url = $db->get_data( $db->zoom_live_url_table, "(student = '0' OR student = '".$current_user->ID."') AND course_id = '".$post->ID."'", true ); 
                }
                
                if( $join_class_url ) {
                    
                    $views = Views::get_instance();

                    $html = $views->load_view( 'front/shortcode/join_class_url', [ 'join_class_url' => $join_class_url ] );
                }
            }
        }
        
        return $html; 
    }
    
    /*
     * @param $content mixed HTML contents
     * 
     * @return $content modified HTML contents
     */
    public function content_filter( $content ) {
        
        global $post;
        
        if( $post->post_type == 'sfwd-courses' ) {
            
            $content = $content.do_shortcode( '[kiflayn_learndash_join_class_url]' );
        }
        
        return $content;
    }
    
    /**
    * Add sub menu page
    *
    * @since 1.0.0
    */
    public function add_live_class_menu() {
        
        add_submenu_page( 'kiflayn_learndash_settings', 
            esc_html__( 'Create Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            esc_html__( 'Create Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            'manage_options',
            'kiflayn_learndash_live_class',
            [ $this, 'create_live_class_link_admin_page' ] 
        );
    }
    
    /*
     * Ajax function to load the course students
     */
    public function get_students_select_box( $course_id, $ajax = true ) {
        
        $error = false;
        $html = $message = '';
        
        if( empty($course_id) ) {
            $course_id = filter_input( INPUT_POST, 'course_id' );
        }
        
        if( empty( $course_id ) ) {
            $error = true;
            $message = __( 'Course id not provided.', KIFLAYN_LEARNDASH_TEXT_DOMAIN );
        }
        
        if( !$error ) {
            
            $views = Views::get_instance();
            
            $course_access_users = learndash_get_course_users_access_from_meta( $course_id );
            
            $students = [];
            if( is_array( $course_access_users ) && sizeof( $course_access_users ) > 0 ) {
                
                $args = [ 'include' => $course_access_users ];                
                $students = get_users( $args );                
            }
            
            $html = $views->load_view( 'admin/students_select_box', [ 'students' => $students ] );
        }
        
        if( $ajax ) {
            
            $return = array( 'error' => $error, 'message'=> $message, 'html' => $html );
            wp_send_json($return); 
        }else{
            
            return $html;
        }
    }
    
    /*
     * Function to show the create live class admin page output 
     * 
     * @since 1.0.0
     */
    public function create_live_class_link_admin_page() {
        
        $views = Views::get_instance();
        
        $message = '';
        
        //Load page only if LearnDash LMS is active
        if( function_exists( 'learndash_min_asset' ) ) {
            
            $message = '';
            
            //$options_instance = Options::get_instance();
            //$options = $options_instance->get_plugin_options(); 
            
            $zoom = Zoom::get_instance();
            
            $zoom_users = $zoom->zoom_api_call( '/users?status=active&page_size=300&page_number=0' );//$options['zoom_users'];
            
            if( isset($zoom_users['users']) && is_array($zoom_users['users']) ) {
                $key_values = array_column($zoom_users['users'], 'first_name' ); 
                array_multisort($key_values, SORT_ASC, $zoom_users['users'] );
            }
            
            $courses = get_posts([
                        'post_type' => 'sfwd-courses',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                      ]);
            
            if( isset($_POST['btnsave']) && $_POST['btnsave'] != "" ) { 
                
                $zoom_user_id   = filter_input( INPUT_POST, 'zoom_user' );                
                $course_id      = filter_input( INPUT_POST, 'course' );
                $url_for        = filter_input( INPUT_POST, 'live_class_url_for' );                
                $students       = filter_input( INPUT_POST, 'students', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
                
                $this->save_zoom_join_url_for_course( $course_id, $zoom_user_id, $students, $url_for );
                
                //$this->update_learndash_course( $course_id );
                
                $message = $views->load_admin_alerts( 'message', 'Join Live Class URL(s) saved successfully!' );
            }
            
            $html = $views->load_view( 'admin/create_live_class', ['message' => $message, 'show_form' => true, 'zoom_users' => $zoom_users['users'], 'courses' => $courses ] );
            
        }else{
            
            $message = $views->load_admin_alerts( 'error', 'You must have LearnDash LMS plugin installed and active.' );
            
            $html = $views->load_view( 'admin/create_live_class', ['message' => $message, 'show_form' => false ] );
        }
        
        echo $html;
    }
    
    /*
     * Function to save the Zoom join URL for the given course
     * 
     * @param $course_id LearnDash Course ID
     * @param $zoom_user_id Zoom user id
     * @param $students array of wordpress users (id:display_name)
     * @param $url_for either create join url for individual or for the group
     * 
     */
    public function save_zoom_join_url_for_course( $course_id, $zoom_user_id, $students, $url_for = 'individual' ) {
        
        //ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
        
        if( is_numeric( $course_id ) && !empty( $zoom_user_id ) ) {
                    
            $zoom = Zoom::get_instance();
            $db = Db::get_instance();
            
            $zapier = Zapier::get_instance();
            $options = Options::get_instance();
            $options = $options->get_plugin_options();
            $zapier_url_live_class = isset($options['zapier_url_live_class'])?$options['zapier_url_live_class']:"";
            
            $teacher = $zoom->zoom_api_call( '/users/'.$zoom_user_id );
            
            if( is_array($students) && sizeof( $students ) > 0 ) {
                
                //Delete old records
                //$db->del_record( $db->zoom_live_url_table, "course_id = '".$course_id."'" );
                
                if( $url_for == 'individual' ) {

                    foreach( $students as $str ) {

                        $user = $this->get_user_data_from_str( $str );
                        
                        $meeting_title = get_the_title( $course_id )." - ".$user['display_name'];

                        $zoom_meeting = $zoom->create_zoom_meeting( $meeting_title, $zoom_user_id );     
                        
                        if( isset( $zoom_meeting['join_url'] ) ) {
                            
                            $zapier->zapier_webhook( $zapier_url_live_class, [ 'liveClassUrl' => $zoom_meeting['join_url'], 'teacher' => $teacher['first_name'].' '.$teacher['last_name'], 'userLogin' => $user['user_login'], 'userDisplayName' => $user['display_name'], 'course' => get_the_title( $course_id ) ] );
                            
                            $db->del_record( $db->zoom_live_url_table, "course_id = '".$course_id."' AND student = '".$user['user_id']."'" );
                            
                            $db->add_record( $db->zoom_live_url_table, [ 'url' => $zoom_meeting['join_url'], 'type' => 'private', 'course_id' => $course_id, 'student' => $user['user_id'], 'timestamp' => strtotime('now') ] );
                        }
                    }

                }else{
                    
                    $display_names = [];

                    foreach( $students as $str ) {

                        $user = $this->get_user_data_from_str( $str );
                        $display_names[] = $user['display_name'];
                    }

                    $meeting_title = get_the_title( $course_id )." - GROUP ( ".implode(", ", $display_names)." )";

                    $zoom_meeting = $zoom->create_zoom_meeting( $meeting_title, $zoom_user_id );

                    if( isset( $zoom_meeting['join_url'] ) ) {
                        
                        $users_display_names= [];
                        $users_login_names  = [];
                        
                        foreach( $students as $str ) {

                            $user = $this->get_user_data_from_str( $str );
                            
                            $users_display_names[]  = $user['display_name'];
                            $users_login_names[]    = $user['user_login'];
                            
                            $db->del_record( $db->zoom_live_url_table, "course_id = '".$course_id."' AND student = '".$user['user_id']."'" );
                            
                            $db->add_record( $db->zoom_live_url_table, [ 'url' => $zoom_meeting['join_url'], 'type' => 'private', 'course_id' => $course_id, 'student' => $user['user_id'], 'timestamp' => strtotime('now') ] );
                        }
                        
                        $zapier->zapier_webhook( $zapier_url_live_class, [ 'liveClassUrl' => $zoom_meeting['join_url'], 'teacher' => $teacher['first_name'].' '.$teacher['last_name'], 'userLogin' => implode( "|", $users_login_names ), 'userDisplayName' => implode( "|", $users_display_names ), 'course' => get_the_title( $course_id ) ] );
                    }
                }
            }else{
                
                $meeting_title = get_the_title( $course_id );

                $zoom_meeting = $zoom->create_zoom_meeting( $meeting_title, $zoom_user_id );

                if( isset( $zoom_meeting['join_url'] ) ) {
                    
                    $db->add_record( $db->zoom_live_url_table, [ 'url' => $zoom_meeting['join_url'], 'type' => 'public', 'course_id' => $course_id, 'student' => 0, 'timestamp' => strtotime('now') ] );
                    
                    $zapier->zapier_webhook( $zapier_url_live_class, [ 'liveClassUrl' => $zoom_meeting['join_url'], 'teacher' => $teacher['first_name'].' '.$teacher['last_name'], 'userLogin' => 0, 'userDisplayName' => 0, 'course' => get_the_title( $course_id ) ] );
                }
            }
        }
        
    }
    
    /*
     * Function to get the user data
     * 
     * @param $str string user
     */
    public function get_user_data_from_str( $str ) {
        
        $user_arr           = explode( ":", $str );
        
        $data['user_id']        = (int)$user_arr[0];
        $data['display_name']   = $user_arr[1];
        $data['user_login']     = $user_arr[2];
        
        return $data;
    }
    
    /*
     * Function to update the LearnDash course
     * 
     * @param $course_id interger LearnDash LMS course id
     * @param $course_users array of users those should assign to LearnDash Course     * 
     * 
     */
    public function update_learndash_course( $course_id ) {
        
        $course     = get_post( $course_id );
        $content    = $course->post_content;
        
        if( strpos( $content, '[kiflayn_learndash_join_class_url]' ) === FALSE ) {
            $content.= '[kiflayn_learndash_join_class_url]';
        }
        
        $data = array(
            'ID' => $course_id,
            'post_content' => $content
           );

        wp_update_post( $data );
    }
}