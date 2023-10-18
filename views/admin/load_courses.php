<?php
if( $single_select == 1 ) {
    echo '<select name="course" id="ld_lms_course">';
    echo '<option value="">'.__( 'Select Course', KIFLAYN_LEARNDASH_TEXT_DOMAIN ).'</option>';
    if( is_array($courses) && sizeof($courses) > 0 ) {
        foreach( $courses as $course ) {
            if( is_array($courses_ids) && sizeof($courses_ids) > 0 ) {
                if(in_array($course->ID, $courses_ids)) {
                    echo '<option value="'.$course->ID.'">'.$course->post_title.'</option>';
                }
            }else{
                echo '<option value="'.$course->ID.'">'.$course->post_title.'</option>';
            }
        }
    }
    echo '</select>';
    echo '<div class="kiflayn_learndash_ajax_loader"></div>';
}else{
    echo '<ul>';
    if( is_array($courses) && sizeof($courses) > 0 ) {
        foreach( $courses as $course ) {
            if(in_array($course->ID, $courses_ids)) {
                echo '<li><input checked type="checkbox" name="course" value="'.$course->ID.'" /> '.$course->post_title.'</li>';
            }else{
                echo '<li><input type="checkbox" name="course" value="'.$course->ID.'" /> '.$course->post_title.'</li>';
            }
        }
    }
    echo '</ul>';
}
?>