<?php if ( $message!="" ) { echo $message; }?>
<div class="wrap">
<h2><?php echo __( 'Create Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></h2>
<table class="wp-list-table widefat fixed" cellspacing="0">
	<thead>
        <tr>
            <th scope="col" class="manage-column" style=""><?php echo __( 'Create Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></th>
        </tr>
	</thead>
	<tbody id="the-list">
        <tr>
            <td align="center">
                <?php if( $show_form ) { ?>
            	<form method="post" class="frm_kiflayn_learndash" enctype="multipart/form-data">
                <table width="100%" class="form-table">
                    <tr valign="top">
                    	<td width="180"><?php echo __( 'Teacher', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></td>
                        <td>
                            <select name="zoom_user" id="zoom_user">
                                <option value=""><?php echo __( 'Select Teacher', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></option>
                                <?php 
                                if( is_array($zoom_users) && sizeof($zoom_users) > 0 ) {
                                    foreach( $zoom_users as $zoom_user ) {
                                ?>
                                        <option value="<?php echo $zoom_user['id'];?>"><?php echo $zoom_user['first_name'].' '.$zoom_user['last_name'];?><?php echo !empty($zoom_user['email'])?' ( '.$zoom_user['email'].' )':'';?></option>
                                <?php 
                                    }
                                }
                                ?>
                            </select>
                            <div class="kiflayn_learndash_ajax_loader_teacher"></div>
                        </td>
                    </tr>
                    <tr valign="top" class="kiflayn_learndash_course">
                        <td>
                            <?php echo __( 'Course', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>
                        </td>
                        <td class="ld_lms_course">
                            
                        </td>
                    </tr>
                    <tr valign="top" class="kiflayn_learndash_hidden">
                        <td>
                            <?php echo __( 'Student(s)', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>
                        </td>
                        <td id="ld_lms_course_users">
                            
                        </td>
                    </tr>
                    <tr valign="top" class="kiflayn_learndash_hidden">
                        <td>
                            <?php echo __( 'Create live class url for?', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>
                        </td>
                        <td>
                            <select name="live_class_url_for">
                                <option value="individual"><?php echo __( 'Separate live class URL for each student', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></option>
                                <option value="group"><?php echo __( 'A single live class URL for all selected students', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></option>
                            </select> 
                        </td>
                    </tr>
                    <tr valign="top" class="kiflayn_learndash_hidden">
                        <th></th>
                        <td>
                            <input type="submit" name="btnsave" id="btnsave" value="<?php echo __( 'Create Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>" class="button button-primary">
                        </td>
                    </tr>
                </table>
                </form>
                <?php }else{ ?>
                <p><?php echo __( 'No Content', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></p>
                <?php }?>
            </td>
        </tr>
     </tbody>
</table>

</div>