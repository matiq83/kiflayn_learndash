<?php
/*
 * Create Recordings class
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Create_Recordings {
    
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
        add_action( 'admin_menu', [ $this, 'add_create_recordings_menu' ] );
        
        add_shortcode( 'kiflayn_learndash_recordings', [ $this, 'learndash_recordings_shortcode' ] );
        
        add_filter( 'the_content', [ $this, 'content_filter' ] );
        
        add_action( 'wp_ajax_kiflayn_learndash_load_zoom_meetings', [ $this, 'load_zoom_meetings' ] );
        add_action( 'wp_ajax_kiflayn_learndash_process_zoom_meetings', [ $this, 'process_zoom_meetings' ] );
    }
    
    /*
     * Shortcode to show the lessons recordings
     */
    public function learndash_recordings_shortcode() {
        
        $html = '';
        
        global $post;
        
        if( $post->post_type == 'sfwd-lessons' ) {
            
            $current_user = wp_get_current_user();
            
            if ( 0 == $current_user->ID ) {
                return $html;
            } else {
                
                $db         = Db::get_instance();
                
                $allowed_roles = [ 'administrator' ];
                
                if( array_intersect($allowed_roles, $current_user->roles ) ) {                    
                    $recordings = $db->get_data( $db->lessons_recordings_table, "lesson_id = '".$post->ID."' ORDER BY recording_date DESC" ); 
                }else{                    
                    $recordings = $db->get_data( $db->lessons_recordings_table, "(student = '0' OR student = '".$current_user->ID."') AND lesson_id = '".$post->ID."' ORDER BY recording_date DESC" ); 
                }
                
                if( $recordings ) {

                    $views = Views::get_instance();

                    $html = $views->load_view( 'front/shortcode/learndash_lesson', [ 'recordings' => $recordings ] );
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
        
        if( $post->post_type == 'sfwd-lessons' ) {
            
            $content = do_shortcode( '[kiflayn_learndash_recordings]' ).$content;
        }
        
        return $content;
    }
    
    /**
    * Add sub menu page
    *
    * @since 1.0.0
    */
    public function add_create_recordings_menu() {
        
        add_submenu_page( 'kiflayn_learndash_settings', 
            esc_html__( 'Create Recordings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            esc_html__( 'Create Recordings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ),
            'manage_options',
            'kiflayn_learndash_create_recordings',
            [ $this, 'create_recordings_admin_page' ] 
        );
        
        if( $_GET['update_recording_date'] == 'yes' ) {
            echo $recording_date = strtotime(str_replace( "/","-", trim('10/09/2023')));
            exit();
            $db = Db::get_instance();
            $rows = $db->get_data( $db->lessons_recordings_table );
            foreach( $rows as $row ) {
                if( empty($row->recording_date ) ) {
                    $recording_date = strtotime(str_replace( "/","-", trim(str_replace( ["Live Class - ", "Live Class: "], "", $row->title ))));
                    //echo $row->id.' - '.$recording_date.'<br>';
                    if( empty($recording_date )) {
                        $recording_date = $row->timestamp;
                    }
                    $db->update_record( $db->lessons_recordings_table, [ 'recording_date' => $recording_date ], "id = '".$row->id."'" );
                    //exit();
                }
            }
            echo 'done';exit();
        }
    }
    
    /*
     * Ajax function to load the Zoom meetings table
     */
    public function load_zoom_meetings() {
        
        $error = false;
        $html = $message = '';
        
        $zoom_user = filter_input( INPUT_POST, 'zoom_user' );
        $course_id = filter_input( INPUT_POST, 'course_id' );
        
        if( empty( $zoom_user ) ) {
            $error = true;
            $message = __( 'Teacher not provided.', KIFLAYN_LEARNDASH_TEXT_DOMAIN );
        }
        
        if( !$error ) {
            
            $views = Views::get_instance();
            
            $zoom = Zoom::get_instance();
            
            $recordings = $zoom->zoom_api_call( '/users/'.$zoom_user.'/recordings?page_size=300&from='.date('Y-m-d', strtotime('-1 years')).'&to='.date('Y-m-d') );
            
            $final_meetings = $recordings['meetings']; 
            
            $live_class = Live_Class::get_instance();
            
            $students_select_box = $live_class->get_students_select_box( $course_id, false );
            
            $html = $views->load_view( 'admin/load_zoom_meetings', [ 'meetings' => $final_meetings, 'students_select_box' => $students_select_box ] );
        }
        
        $return = array( 'error' => $error, 'message'=> $message, 'html' => $html );
        wp_send_json($return); 
    }
    
    /*
     * Ajax function to process the zoom meeting
     */
    public function process_zoom_meetings() {
        
        $task               = filter_input( INPUT_POST, 'task' );        
        $course_id          = filter_input( INPUT_POST, 'course_id' );
        $teacher_id         = filter_input( INPUT_POST, 'teacher_id' );
        $zoom_recording_id  = filter_input( INPUT_POST, 'zoom_recording_id' );
        $zoom_meeting_id    = filter_input( INPUT_POST, 'zoom_meeting_id' );
        $zoom_meeting_uuid  = filter_input( INPUT_POST, 'zoom_meeting_uuid' );
        $zoom_meeting_number= filter_input( INPUT_POST, 'zoom_meeting_number' );
        $zoom_video_title   = filter_input( INPUT_POST, 'zoom_video_title' );
        $zoom_meeting_title = filter_input( INPUT_POST, 'zoom_meeting_title' );
        $students           = filter_input( INPUT_POST, 'students', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        
        if( empty($zoom_video_title) ) {
            $zoom_video_title = $zoom_meeting_title;
        }
        //ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
        
        //If a UUID starts with "/" or contains "//" (example: "/ajXp112QmuoKj4854875=="), we must double encode the UUID before making an API request.
        if( strpos( $zoom_meeting_uuid, "/" ) === 0 ) {
            $zoom_meeting_uuid = urlencode(urlencode( $zoom_meeting_uuid ));
        }
            
        $zoom = Zoom::get_instance();
        
        $message = '';
        $error = false;
        
        if( $task == 'delete' ) {            
            $response = $zoom->zoom_api_call( '/meetings/'.$zoom_meeting_uuid.'/recordings/'.$zoom_recording_id, 'DELETE' );            
            $message = 'Meeting deleted successfully.';            
        }elseif( $task == 'upload' ) {
            
            $zoom_recordings = $zoom->zoom_api_call( '/meetings/'.$zoom_meeting_uuid.'/recordings' );
            $teacher = $zoom->zoom_api_call( '/users/'.$teacher_id );
            
            $zapier = Zapier::get_instance();
            $options = Options::get_instance();
            $options = $options->get_plugin_options();
            $zapier_url_recordings = isset($options['zapier_url_recordings'])?$options['zapier_url_recordings']:"";
            
            $files = $zoom_recordings['recording_files'];

            if( !empty($files[0]['download_url']) ) {

                if( !empty($zoom_video_title) ) {

                    foreach( $files as $zoom_video_file ) {
                        $zoom_file_type = $zoom_video_file['file_type'];
                        if( $zoom_file_type == 'MP4' ) {
                            $zoom_video_files[] = $zoom_video_file['download_url'];
                        }
                    }

                    $counter = 1;
                    $total_files = sizeof($zoom_video_files);
                    $old_timestamp = strtotime('-1 Hour');

                    $vimeo = VimeoUpload::get_instance();
                    $db = Db::get_instance();

                    $video_responses = [];

                    foreach( $zoom_video_files as $zoom_video ) {

                        $part = '';
                        if( $counter > 1 ) {
                            $part = ' - part '.$counter;
                        }

                        $timestamp = strtotime('now');
                        $tmp_video = KIFLAYN_LEARNDASH_PLUGIN_PATH.'tmp/'.$timestamp."_video.mp4";
                        $tmp_video_link = KIFLAYN_LEARNDASH_PLUGIN_URL.'tmp/'.$timestamp."_video.mp4";

                        file_put_contents($tmp_video, $zoom->get_download_video_file_data($zoom_video));
                        
                        $meetingDate = date( 'd/m/Y', strtotime( $zoom_recordings['start_time'] ) );
                        $vimeo_video_title  = $zoom_meeting_title.' - '.$meetingDate;
                        $video_title        = $zoom_video_title.' - '.$meetingDate;

                        $video_response = $vimeo->upload_video( $tmp_video_link, $vimeo_video_title );

                        $video_responses[] = [ 'video_response' => $video_response, 'video_title' => $video_title, 'recording_date' => strtotime( $zoom_recordings['start_time'] ) ];

                        $db->add_record( $db->video_files_table, array( 'path' => $tmp_video, 'url' => $tmp_video_link, 'timestamp' => $timestamp ) ); 
                        
                        $zapier->zapier_webhook( $zapier_url_recordings, [ 'teacher' => $teacher['first_name'].' '.$teacher['last_name'], 'course' => get_the_title( $course_id ), 'meetingTitle' => $zoom_meeting_title, 'meetingDate' => $meetingDate ] );
                        
                        $counter++;
                        $message = 'Video uploaded successfully!';   
                    }

                    $this->save_data_for_learndash_lesson( $video_responses, $course_id, $students );

                }else{

                    $error = true;
                    $message = 'You not provide video title for meeting "'.$zoom_meeting_title.'"';
                }

            }else{

                $error = true;
                $message = 'There is no video url for meeting "'.$zoom_meeting_title.'"';
            }            
        }
        
        $return = array( 'error' => $error, 'message'=> $message, 'zoom_meeting_number' => $zoom_meeting_number );
        wp_send_json($return);         
    }
    
    /*
     * Function to get the user data
     * 
     * @param $str string user
     */
    public function get_user_data_from_str( $str ) {
        
        $user_arr           = explode( ":", $str );
        
        $data['user_id']    = (int)$user_arr[0];
        $data['user_name']  = $user_arr[1];
        
        return $data;
    }
    
    /*
     * Function to save data for LearnDash LMS lesson shortcode
     */
    public function save_data_for_learndash_lesson( $video_responses, $course_id, $students ) {
        
        $html = '';
        
        $db     = Db::get_instance();
        
        if( is_array( $video_responses ) ) {
            
            $post_meta = $db->get_data( 'postmeta', "meta_key = 'course_id' AND meta_value = '".$course_id."'", true );
            
            if( $post_meta ) {
                $lesson_id = $post_meta->post_id;
            }else{
                $post_name = sanitize_title( 'Class Recordings for '.get_the_title( $course_id ) );
                // Create post object
                $lesson = array(
                    'post_title'      => wp_strip_all_tags( 'Class Recordings' ),
                    'post_content'    => '',
                    'post_type'       => 'sfwd-lessons',
                    'post_name'       => $post_name,
                    'post_status'     => 'publish'              
                );

                // Insert the post into the database
                $lesson_id = wp_insert_post( $lesson );
                
                //Link lesson with course
                update_post_meta( $lesson_id, 'course_id', $course_id );
                update_post_meta( $lesson_id, '_sfwd-lessons', $this->get_swf_default_lesson_data( $course_id ) );
            }
            
            //Delete old records
            //$db->del_record( $db->lessons_recordings_table, "course_id = '".$course_id."'" );
                
            foreach( $video_responses as $video_response ) {
                
                $video              = $video_response['video_response'];
                $zoom_video_title   = $video_response['video_title'];
                
                $data = [];
                $data['title']          = $zoom_video_title;
                $data['embed_code']     = $video['body']['player_embed_url'];
                $data['course_id']      = $course_id;
                $data['lesson_id']      = $lesson_id;
                $data['recording_date'] = $video_response['recording_date'];
                $data['timestamp']      = strtotime( 'now' );
                $data['student']        = 0;
                
                if( is_array($students) ) {
                    
                    foreach( $students as $str ) {

                        $user           = $this->get_user_data_from_str( $str );
                        $data['student']= $user['user_id'];

                        $db->add_record( $db->lessons_recordings_table, $data );
                    }    
                }else{
                    $db->add_record( $db->lessons_recordings_table, $data );
                }
            }
        }
        
    }
    
    /*
     * Function to get the default meta data for lesson
     * 
     * @param $course_id LearnDash LMS course ID
     */
    public function get_swf_default_lesson_data( $course_id ) {
        
        $prefix = 'sfwd-lessons_';
        
        $data = [];
                
        $data[$prefix.'lesson_materials_enabled'] = '';
        $data[$prefix.'lesson_materials'] = '';
        $data[$prefix.'lesson_video_enabled'] = '';
        $data[$prefix.'lesson_video_url'] = '';
        $data[$prefix.'lesson_video_shown'] = '';
        $data[$prefix.'lesson_video_auto_start'] = '';
        $data[$prefix.'lesson_video_show_controls'] = '';
        $data[$prefix.'lesson_video_focus_pause'] = '';
        $data[$prefix.'lesson_video_track_time'] = '';
        $data[$prefix.'lesson_video_auto_complete'] = '';
        $data[$prefix.'lesson_video_auto_complete_delay'] = '';
        $data[$prefix.'lesson_video_show_complete_button'] = '';
        $data[$prefix.'lesson_assignment_upload'] = '';
        $data[$prefix.'assignment_upload_limit_extensions'] = '';
        $data[$prefix.'assignment_upload_limit_size'] = '';
        $data[$prefix.'lesson_assignment_points_enabled'] = '';
        $data[$prefix.'lesson_assignment_points_amount'] = '';        
        $data[$prefix.'assignment_upload_limit_count'] = '';
        $data[$prefix.'lesson_assignment_deletion_enabled'] = '';
        $data[$prefix.'auto_approve_assignment'] = '';
        $data[$prefix.'forced_lesson_time_enabled'] = '';
        $data[$prefix.'forced_lesson_time'] = '';
        $data[$prefix.'lesson_video_hide_complete_button'] = '';
        $data[$prefix.'lesson_schedule'] = '';
        $data[$prefix.'course'] = $course_id;
        $data[$prefix.'sample_lesson'] = '';
        $data[$prefix.'visible_after'] = '';
        $data[$prefix.'visible_after_specific_date'] = '';
        
        return $data;
    }
    
    /*
     * Function to create the admin page for the create recordings
     * 
     * @since 1.0.0
     */
    public function create_recordings_admin_page() {
        
        $views = Views::get_instance();
        
        $message = '';
        
        //Load page only if LearnDash LMS is active
        if( function_exists( 'learndash_min_asset' ) ) {
            
            $message = '';
            
            $options_instance = Options::get_instance();

            $options = $options_instance->get_plugin_options(); 

            $zoom = Zoom::get_instance();
            
            $zoom_users = $zoom->zoom_api_call( '/users?status=active&page_size=300&page_number=0' );
            
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
            
            $html = $views->load_view( 'admin/create_recordings', ['message' => $message, 'show_form' => true, 'zoom_users' => $zoom_users['users'], 'courses' => $courses ] );
            
        }else{
            
            $message = $views->load_admin_alerts( 'error', 'You must have LearnDash LMS plugin installed and active.' );
            
            $html = $views->load_view( 'admin/create_recordings', ['message' => $message, 'show_form' => false ] );
        }
        
        echo $html;
    }
}