// JavaScript Document
jQuery(document).ready(function($) {
    
    if( $(".frm_kiflayn_learndash #zoom_user").length ) {
        
        $(".frm_kiflayn_learndash #zoom_user").change(function(){
            
            kiflayn_learndash_load_courses( $(this).val() );
            //$(".frm_kiflayn_learndash #ld_lms_course").val("");
            $("#zoom_meetings_container").html( "" );
            $(".kiflayn_learndash_hidden").hide();            
        });
    }
    
    //Function to load the teacher courses
    function kiflayn_learndash_load_courses( teacher_id ) {
        
        $(".kiflayn_learndash_ajax_loader_teacher").hide();
        $(".kiflayn_learndash_course").hide();
            
        if( teacher_id ) {
            $(".kiflayn_learndash_ajax_loader_teacher").show();
            var single_select = 1;
            if( $(".frm_kiflayn_learndash_assign_courses").length ) {
                single_select = 0;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: kiflayn_learndash_data.ajaxurl,
                data: {
                    action: 'kiflayn_learndash_load_courses',
                    teacher_id: teacher_id,
                    single_select: single_select
                },
                success: function(response) {
                    if(response.error) {
                        console.log(response.message);
                    }else{
                        $(".kiflayn_learndash_course .ld_lms_course").html( response.html );
                        $(".kiflayn_learndash_course").show();  
                        
                        if( $(".frm_kiflayn_learndash #ld_lms_course").length ) {
        
                            $(".frm_kiflayn_learndash #ld_lms_course").change(function(){

                                var course_id = $(this).val();

                                if( course_id ) {

                                    $(".kiflayn_learndash_ajax_loader").show();

                                    if( $("#ld_lms_course_users").length ) {

                                        kiflayn_learndash_load_students( course_id );

                                    }else if( $("#zoom_meetings_container").length ) {

                                        var zoom_user = $("#zoom_user").val();

                                        kiflayn_learndash_load_zoom_meetings( course_id, zoom_user );
                                    }
                                }
                            });
                        }
                        
                        if( $(".frm_kiflayn_learndash .kiflayn_learndash_course input[type='checkbox']").length ) {
                            $(".frm_kiflayn_learndash .kiflayn_learndash_course input[type='checkbox']").change(function(){
                                kiflayn_learndash_assign_tearcher_courses();
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert("There is some error to connect with server. Please try again.");
                }
            }).always(function(){  
                $(".kiflayn_learndash_ajax_loader_teacher").hide();                        
            });
        }
    }
    
    //Function to assign courses to teacher
    function kiflayn_learndash_assign_tearcher_courses() {
        
        var teacher_id = $('#zoom_user').val();
        var courses = [];
        $(".frm_kiflayn_learndash .kiflayn_learndash_course input[type='checkbox']:checked").each(function () {
            courses.push($(this).val());
        });
        
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: kiflayn_learndash_data.ajaxurl,
            data: {
                action: 'kiflayn_learndash_assign_tearcher_courses',
                courses: courses,
                teacher_id:teacher_id
            },
            success: function(response) {
                if(response.error) {
                    console.log(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("There is some error to connect with server. Please try again.");
            }
        }).always(function(){  
                            
        });
    }
    
    //Function to load the students
    function kiflayn_learndash_load_students( course_id ) {
        
        if( course_id ) {
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: kiflayn_learndash_data.ajaxurl,
                data: {
                    action: 'kiflayn_learndash_load_students',
                    course_id: course_id
                },
                success: function(response) {
                    if(response.error) {
                        console.log(response.message);
                    }else{
                        $("#ld_lms_course_users").html( response.html );
                        $(".kiflayn_learndash_hidden").show();                                
                    }
                },
                error: function (xhr, status, error) {
                    alert("There is some error to connect with server. Please try again.");
                }
            }).always(function(){  
                $(".kiflayn_learndash_ajax_loader").hide();                        
            });
        }
    }
    
    //Function to load the zoom meetings
    function kiflayn_learndash_load_zoom_meetings( course_id, zoom_user ) {
        
        if( zoom_user && course_id ) {
                
            $(".kiflayn_learndash_ajax_loader").show();
            $("#zoom_meetings_container").html( "" );
            $(".kiflayn_learndash_hidden").hide();
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: kiflayn_learndash_data.ajaxurl,
                data: {
                    action: 'kiflayn_learndash_load_zoom_meetings',
                    zoom_user:zoom_user,
                    course_id:course_id
                },
                success: function(response) {
                    if(response.error) {
                        console.log(response.message);
                    }else{
                        $("#zoom_meetings_container").html( response.html );
                        $(".kiflayn_learndash_hidden").show();
                        
                        if( $(".zoom_videos_table .button").length ) {
                            $(".zoom_videos_table .button").click(function(){
                                kiflayn_learndash_process_zoom_meetings(this);
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert("There is some error to connect with server. Please try again.");
                }
            }).always(function(){  
                $(".kiflayn_learndash_ajax_loader").hide();
            });
        }
    }
    
    //Function to process the zoom meetings
    function kiflayn_learndash_process_zoom_meetings(obj) {
        
        var task = $(obj).attr("data-action");
        if( task == 'delete' ) {
            if( !confirm('Are you sure you want to delete that meeting?') ) {
                return true;
            }
        }
        var zoom_recording_id = $(obj).attr("data-recording-id");
        var zoom_meeting_id = $(obj).attr("data-meeting-id");
        var zoom_meeting_uuid = $(obj).attr("data-meeting-uuid");
        var zoom_meeting_number = $(obj).attr("data-meeting-number");
        $(".zoom_meetings_actions_"+zoom_meeting_number+" .button").hide();
        $(".zoom_meetings_actions_"+zoom_meeting_number+" img").show();
        var zoom_video_title = $("#zoom_video_title_"+zoom_meeting_number).val();
        var zoom_meeting_title = $("#zoom_meeting_title_"+zoom_meeting_number).val();
        var teacher_id = $("#zoom_user").val();
        var course_id = $("#ld_lms_course").val();
        
        var students = [];
        $(".zoom_meetings_row_"+zoom_meeting_number+" select[name='students[]']").each(function() {
            students.push( $(this).val() );
        });
      
        var data = {
            action: 'kiflayn_learndash_process_zoom_meetings',
            zoom_meeting_id: zoom_meeting_id,
            zoom_meeting_uuid: zoom_meeting_uuid,
            zoom_recording_id: zoom_recording_id,
            zoom_meeting_number: zoom_meeting_number,
            course_id: course_id,
            task: task,
            teacher_id:teacher_id,
            zoom_video_title:zoom_video_title,
            zoom_meeting_title:zoom_meeting_title,
            students: students[0]
        }
        $.post( ajaxurl, data, function( response ) {
            $(".zoom_meetings_actions_"+response.zoom_meeting_number+" img").hide();
            if( response.message != "" ) {
                $(".zoom_meetings_actions_"+response.zoom_meeting_number).append('<p>'+response.message+'</p>');
                setTimeout(function(){
                    $(".zoom_meetings_actions_"+response.zoom_meeting_number+" p").remove();
                    $(".zoom_meetings_actions_"+response.zoom_meeting_number+" .button").show();  
                    if( task == 'delete' ) {
                        $(".zoom_meetings_row_"+response.zoom_meeting_number).remove();
                    }
                },4000);
            }else{
                $(".zoom_meetings_actions_"+response.zoom_meeting_number+" .button").show();            
            }
        });
    }
});