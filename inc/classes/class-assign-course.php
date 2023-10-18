<?php
/*
 * Create Join Live Class URL
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Assign_Course {
    
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
        
        add_action( 'wp_ajax_kiflayn_learndash_load_courses', [ $this, 'learndash_load_courses' ] );
        add_action( 'wp_ajax_kiflayn_learndash_assign_tearcher_courses', [ $this, 'assign_tearcher_courses' ] );
    }
    
    /*
     * Ajax function to assign courses to the teacher
     */
    public function assign_tearcher_courses() {
        
        $error  = false;
        $message= "";
        
        $teacher_id     = filter_input( INPUT_POST, 'teacher_id' );
        $courses        = filter_input( INPUT_POST, 'courses', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        
        $db = Db::get_instance();
        $db->del_record( $db->teacher_courses_table, "teacher_id = '".$teacher_id."'" );
            
        if( is_array( $courses ) ) {
            
            $data = [];
            
            foreach( $courses as $course ) {
                $row = [];
                $row['teacher_id'] = $teacher_id;
                $row['course_id']  = $course;
                $row['timestamp']  = strtotime('now');
                $data[] = $row;
            }
            
            $attr = [ 'teacher_id', 'course_id', 'timestamp' ];
            $db->add_multiple_records( $db->teacher_courses_table, $attr, $data );
        }
        
        $return = array( 'error' => $error, 'message'=> $message );
        wp_send_json($return); 
    }
    
    /*
     * Ajax function to load the teacher courses
     */
    public function learndash_load_courses() {
        
        $error  = false;
        $message= "";
        
        $single_select  = filter_input( INPUT_POST, 'single_select' );
        $teacher_id     = filter_input( INPUT_POST, 'teacher_id' );
        
        $db = Db::get_instance();
        
        $teacher_courses = $db->get_data( $db->teacher_courses_table, "teacher_id = '".$teacher_id."'" );
        $courses_ids = [];
        if( is_array($teacher_courses) ) {
            foreach( $teacher_courses as $teacher_course ) {
                $courses_ids[] = $teacher_course->course_id;
            }
        }
        
        $courses = get_posts([
                        'post_type' => 'sfwd-courses',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                      ]);
        
        $views = Views::get_instance();
        
        $html = $views->load_view( 'admin/load_courses', ['courses_ids' => $courses_ids, 'courses' => $courses, 'teacher_id' => $teacher_id, 'single_select' => $single_select ] );
        
        $return = array( 'error' => $error, 'message'=> $message, 'html' => $html );
        wp_send_json($return); 
    }
    
    /**
    * Add sub menu page
    *
    * @since 1.0.0
    */
    public function add_live_class_menu() {
        
        add_submenu_page( 'kiflayn_learndash_settings', 
            esc_html__( 'Assign Courses', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            esc_html__( 'Assign Courses', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            'manage_options',
            'kiflayn_learndash_assign_courses',
            [ $this, 'create_assign_courses_admin_page' ] 
        );
    }
    
    /*
     * Function to show the create live class admin page output 
     * 
     * @since 1.0.0
     */
    public function create_assign_courses_admin_page() {
        
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
                                
                $message = $views->load_admin_alerts( 'message', 'Join Live Class URL(s) saved successfully!' );
            }
            
            $html = $views->load_view( 'admin/assign_courses', ['message' => $message, 'show_form' => true, 'zoom_users' => $zoom_users['users'], 'courses' => $courses ] );
            
        }else{
            
            $message = $views->load_admin_alerts( 'error', 'You must have LearnDash LMS plugin installed and active.' );
            
            $html = $views->load_view( 'admin/assign_courses', ['message' => $message, 'show_form' => false ] );
        }
        
        echo $html;
    }
}