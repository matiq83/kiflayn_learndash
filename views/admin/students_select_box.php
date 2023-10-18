<select name="students[]" multiple>
    <?php
    if( is_array( $students ) ) {
        foreach( $students as $user ) {
            echo '<option value="'.$user->ID.':'.$user->display_name.':'.$user->user_login.'">'.$user->display_name.' ('.$user->user_email.')</option>';
        }
    }
    ?>
</select>
<ul>
    <li><?php echo __( 'For windows: Hold down the control (ctrl) button to select multiple options', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></li>
    <li><?php echo __( 'For Mac: Hold down the command button to select multiple options', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></li>
</ul>