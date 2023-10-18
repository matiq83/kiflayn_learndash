<?php
/*
 * Pligin CRON class
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Cron {
    
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
        add_filter( 'cron_schedules', [ $this, 'cron_interval' ] );        
        add_action( KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_event', [ $this, 'run_cron' ] );
    }
    
    /*
     * Cron event hook that will extecuted at custom interval
     */
    public function run_cron() {
        
        $db = Db::get_instance();
        
        $videos = $db->get_data( $db->video_files_table, "timestamp <= ".strtotime("-10 minutes") );
		
        if(is_array($videos) ) {
            foreach( $videos as $video_file ) {
                if( unlink( $video_file->path ) ) {
                    $db->del_record( $db->video_files_table, "id = '".$video_file->id."'" );
                }
            }
        }
    }
    
    /*
     * Function to add custom CRON interval
     * 
     * @param $schedules array of WP schedules
     * 
     * @return $schedules updated array of schedules
     */
    public function cron_interval( $schedules ) {
        
        $schedules[KIFLAYN_LEARNDASH_TEXT_DOMAIN.'_cron_interval'] = array(
            'interval' => 60*60, //60 minutes
            'display'  => esc_html__( 'Every 60 Minutes' ), KIFLAYN_LEARNDASH_TEXT_DOMAIN );
        
        return $schedules;
    }
}