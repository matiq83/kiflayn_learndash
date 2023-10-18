<?php if( is_array($meetings) && sizeof($meetings) > 0 ) { ?>
<table cellpadding="0" cellspacing="0" class="zoom_videos_table">
    <tr>
        <td><b><?php echo __( 'Meeting Title', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></b></td>
        <td><b><?php echo __( 'Meeting Date', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></b></td>
        <td><b><?php echo __( 'Meeting Video Title', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></b></td>
        <td><b><?php echo __( 'Assign To', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></b></td>
        <td></td>
    </tr>
    <?php 
    $counter = 7657;
    foreach( $meetings as $meeting ) {
        $files = $meeting['recording_files'];
        if( is_array($files) ) {
            foreach( $files as $file ) {
                if( $file['file_type'] == 'MP4' ) {
        ?>
                <tr class="zoom_meetings_row_<?php echo $counter;?>">
                    <td><b><?php echo $meeting['topic'];?></b><input type="hidden" name="zoom_meeting_title_<?php echo $counter;?>" id="zoom_meeting_title_<?php echo $counter;?>" value="<?php echo $meeting['topic'];?>" /></td>
                    <td><?php echo $meeting['start_time'];?></td>
                    <td><input type="text" name="zoom_video_title_<?php echo $counter;?>" id="zoom_video_title_<?php echo $counter;?>" value="" /></td>
                    <td><?php echo $students_select_box;?></td>
                    <td class="zoom_meetings_actions zoom_meetings_actions_<?php echo $counter;?>">
                        <input class="button" type="button" data-action="upload" data-meeting-number="<?php echo $counter;?>" data-recording-id="<?php echo $file['id'];?>" data-meeting-uuid="<?php echo $meeting['uuid'];?>" data-meeting-id="<?php echo $meeting['id'];?>" name="btn_upload_video" id="btn_upload_video" value="<?php echo __( 'Upload Video', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>" />
                        <input class="button" type="button" data-action="delete" data-meeting-number="<?php echo $counter;?>" data-recording-id="<?php echo $file['id'];?>" data-meeting-uuid="<?php echo $meeting['uuid'];?>" data-meeting-id="<?php echo $meeting['id'];?>" name="btn_delete_video" id="btn_delete_video" value="<?php echo __( 'Delete Video', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>" />
                        <img src="<?php echo KIFLAYN_LEARNDASH_ASSETS_DIR_URL;?>images/ajax-loader.gif" style="display: none;" /> 
                        <iframe src="<?php //echo $meeting['recording_files'][0]['play_url'];?>" style="display:none;"></iframe>
                    </td>            
                </tr>
        <?php
                }
            }
        }
        $counter++;
    }
    ?>    
</table>
<?php }else{ ?>
<p><?php echo __( 'No video found for the selected teacher', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></p>
<?php }?>