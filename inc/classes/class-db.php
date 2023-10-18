<?php
/*
 * DB class
 * 
 * @package KIFLAYN_LEARNDASH
 */

namespace KIFLAYN_LEARNDASH\Inc;

use KIFLAYN_LEARNDASH\Inc\Traits\Singleton;

class Db {
    
    use Singleton;
    
    //DB Tables names
    public $video_files_table       = 'kiflayn_learndash_video_files';
    public $lessons_recordings_table= 'kiflayn_learndash_lessons_recordings';
    public $zoom_live_url_table     = 'kiflayn_learndash_zoom_live_url';
    public $teacher_courses_table   = 'kiflayn_learndash_teacher_courses';
    
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
     * insert record into database
     * 
     * @param $table table in which we will insert record
     * @param $data array of data that we will insert
     * 
     * return last insert id
     */
    
    public function add_record( $table = '', $data = array() ) {
        
        if( empty($data) || empty($table) ) {
            return false;
        }
        
        global $wpdb;
        $exclude = array( 'btnsave' );
        $attr = "";
        $attr_val = "";
        foreach( $data as $k=>$val ) {            
            if(is_array($val)) {
                $val = maybe_serialize($val);
            }else{
                $val = $this->make_safe($val);
            }
            if( !in_array( $k, $exclude )) { 
                if( $attr == "" ) {
                    $attr.="`".$k."`";
                    $attr_val.="'".str_replace("'", "\\'",$val)."'";
                }else{
                    $attr.=", `".$k."`";
                    $attr_val.=", '".str_replace("'", "\\'",$val)."'";
                }                
            }
        }
        $sql = "INSERT INTO `".$wpdb->prefix.$table."` (".$attr.") VALUES (".$attr_val.")";
        $wpdb->query($sql);
        $lastid = $wpdb->insert_id;
        return $lastid;        
    }
    
    /*
     * insert multiple records into database
     * 
     * @param $table table in which we will insert record
     * @param $data array of data that we will insert
     * 
     * return last insert id
     */
    public function add_multiple_records( $table = '', $attr = array(), $data = array() ) {
        
        if( empty($data) || empty($table) || empty($attr) ) {
            return false;
        }
        
        global $wpdb;
        
        $exclude = array( 'btnsave' );
        $attr_str = "";
        foreach( $attr as $v ) {
            if( $attr_str == "" ) {
                $attr_str.="`".$v."`";
            }else{
                $attr_str.=", `".$v."`";
            }                
        }
        $attr_val = "";
        foreach( $data as $row ) { 
            if( $attr_val == '' ) {
                $attr_val.='(';
            }else{
                $attr_val.=',(';
            }
            $attr_val_row = '';
            
            foreach( $attr as $k ) {
                $val = $row[$k];
                if(is_array($val)) {
                    $val = maybe_serialize($val);
                }else{
                    $val = $this->make_safe($val);
                }
                if( !in_array( $k, $exclude )) {
                    if( $attr_val_row == "" ) {
                        $attr_val_row.="'".str_replace("'", "\\'",$val)."'";
                    }else{
                        $attr_val_row.=", '".str_replace("'", "\\'",$val)."'";
                    }
                }
            }
            
            $attr_val.= $attr_val_row.')';
        }
        $sql = "INSERT INTO `".$wpdb->prefix.$table."` (".$attr_str.") VALUES ".$attr_val;
        $wpdb->query($sql);
        $lastid = $wpdb->insert_id;
        return $lastid;    
    }
    
    /*
     * update record into database
     * 
     * @param $table table for which we will update record
     * @param $data array of data that we will update
     * @param $where string for where clause of sql
     */
    public function update_record( $table = '', $data = array(), $where = '' ) {
        
        if( empty($where) || empty($data) || empty($table) ) {
            return false;
        }
        
        global $wpdb;
        $exclude = array( 'id','btnsave' );
        $attr = "";
        foreach( $data as $k=>$val ) {
            if(is_array($val)) {
                $val = maybe_serialize($val);
            }else{
                $val = $this->make_safe($val);
            }
            if( !in_array( $k, $exclude )) {
                if( $attr == "" ) {
                    $attr.="`".$k."` = '".str_replace("'", "\\'",$val)."'";                    
                }else{
                    $attr.=", `".$k."` = '".str_replace("'", "\\'",$val)."'";
                }                
            }
        }
        $sql = "UPDATE `".$wpdb->prefix.$table."` SET ".$attr." WHERE ".$where;
        $wpdb->query($sql);
        
        return true;
    }
    
    /*
     * delete record from database
     * 
     * @param $table table from which we will delete record
     * @param $where string for where clause of sql
     */
    public function del_record( $table = '', $where = '' ) {
        
        if( empty($where) || empty($table) ) {
            return false;
        }
        
        global $wpdb;
        $sql = "DELETE FROM `".$wpdb->prefix.$table."` WHERE ".$where;
        $wpdb->query($sql);
        return true;
    }
    
    /*
     * get data from the database table
     * 
     * @param $table database table from which we will get records
     * @param $where string for where clause of sql
     * @param $get_row return only one row or all rows
     * @param $attr string 
     * 
     * return a row or all rows objects
     */
    public function get_data( $table = '', $where = "1", $get_row = false, $attr = "*" ) {
        
        if( empty($table) ) {
            return false;
        }
        
        global $wpdb;
        
        $sql = "SELECT ".$attr." FROM `".$wpdb->prefix.$table."` WHERE ".$where;
        if( $get_row ) {
            $data = $wpdb->get_row($sql);
        }else{
            $data = $wpdb->get_results($sql);
        }
        
        return $data;
    }
    
    /*
     * make a variable snaitize and 
     * handel quotes double quotes and other characters 
     * 
     * @param $variable
     * 
     * return snaitizeed variable 
     */
    public function make_safe( $variable ) {

        $variable = sanitize_text_field($variable);
        $variable = esc_html($variable);
        
        return $variable;
    }
    
    /*
     * Function to create teacher courses database table
     */
    public function create_teacher_courses_table() {
        
        global $wpdb;
        
        $sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix.$this->teacher_courses_table."` (
                        `id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `teacher_id` varchar(255) NOT NULL,
                        `course_id` varchar(255) NOT NULL,
                        `timestamp` bigint(20) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        
        $wpdb->query($sql);
    }
    
    /*
     * Function to create video files database table
     */
    public function create_video_files_table() {
        
        global $wpdb;
        
        $sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix.$this->video_files_table."` (
                        `id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `path` varchar(255) NOT NULL,
                        `url` varchar(255) NOT NULL,
                        `timestamp` varchar(255) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        
        $wpdb->query($sql);
    }
    
    /*
     * Function to create lessons recordings database table
     */
    public function create_lessons_recordings_table() {
        
        global $wpdb;
        
        $sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix.$this->lessons_recordings_table."` (
                        `id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `title` varchar(255) NOT NULL,
                        `embed_code` text NOT NULL,
                        `course_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `lesson_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `student` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `recording_date` bigint(20) NOT NULL,
                        `timestamp` bigint(20) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        
        $wpdb->query($sql);
    }
    
    /*
     * Function to create Zoom Join URL database table
     */
    public function create_zoom_live_url_table() {
        
        global $wpdb;
        
        $sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix.$this->zoom_live_url_table."` (
                        `id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `url` varchar(255) NOT NULL,
                        `type` varchar(255) NOT NULL,
                        `course_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `student` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `timestamp` varchar(255) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        
        $wpdb->query($sql);
    }
}