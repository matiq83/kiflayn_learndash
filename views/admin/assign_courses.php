<?php if ( $message!="" ) { echo $message; }?>
<div class="wrap">
<h2><?php echo __( 'Assign Courses', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></h2>
<table class="wp-list-table widefat fixed" cellspacing="0">
	<thead>
        <tr>
            <th scope="col" class="manage-column" style=""><?php echo __( 'Assign Courses', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></th>
        </tr>
	</thead>
	<tbody id="the-list">
        <tr>
            <td align="center">
                <?php if( $show_form ) { ?>
            	<form method="post" class="frm_kiflayn_learndash frm_kiflayn_learndash_assign_courses" enctype="multipart/form-data">
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
                            <?php echo __( 'Assigned Courses', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>
                        </td>
                        <td class="ld_lms_course">
                            
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