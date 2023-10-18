<?php if ( $message!="" ) { echo $message; }?>
<div class="wrap">
    <h2><?php esc_html_e( KIFLAYN_LEARNDASH_PLUGIN_NAME.' Settings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></h2><br>
    <table class="wp-list-table widefat fixed" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" class="manage-column" style=""><?php esc_html_e( KIFLAYN_LEARNDASH_PLUGIN_NAME.' Settings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
        </tr>
        </thead>
        <tbody id="the-list">
        <tr>
            <td>
                <form method="post" class="frm_kiflayn_learndash" action="" enctype="multipart/form-data">
                    <table class="form-table wpex-custom-admin-login-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zapier URL For Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zapier_url_live_class'])?$options['zapier_url_live_class']:""; ?>
                                <input type="text" name="zapier_url_live_class" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zapier URL For Recordings', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zapier_url_recordings'])?$options['zapier_url_recordings']:""; ?>
                                <input type="text" name="zapier_url_recordings" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom API URL', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_api_url'])?$options['zoom_api_url']:""; ?>
                                <input type="text" name="zoom_api_url" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom Account ID', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_account_id'])?$options['zoom_account_id']:""; ?>
                                <input type="text" name="zoom_account_id" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom Client ID', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_client_id'])?$options['zoom_client_id']:""; ?>
                                <input type="text" name="zoom_client_id" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom Client Secret', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_client_secret'])?$options['zoom_client_secret']:""; ?>
                                <input type="text" name="zoom_client_secret" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <?php /* ?>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom API Key', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_api_key'])?$options['zoom_api_key']:""; ?>
                                <input type="text" name="zoom_api_key" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom API Secret', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_api_secret'])?$options['zoom_api_secret']:""; ?>
                                <input type="text" name="zoom_api_secret" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Zoom JWT Token', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['zoom_jwt_token'])?$options['zoom_jwt_token']:""; ?>
                                <input type="text" name="zoom_jwt_token" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <?php */ ?>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Vimeo Client ID', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['vimeo_client_id'])?$options['vimeo_client_id']:""; ?>
                                <input type="text" name="vimeo_client_id" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Vimeo Client Secret Key', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['vimeo_client_secret'])?$options['vimeo_client_secret']:""; ?>
                                <input type="text" name="vimeo_client_secret" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Vimeo Access Token', KIFLAYN_LEARNDASH_TEXT_DOMAIN ); ?></th>
                            <td>
                                <?php $value = isset($options['vimeo_access_token'])?$options['vimeo_access_token']:""; ?>
                                <input type="text" name="vimeo_access_token" value="<?php echo esc_attr( $value ); ?>">
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th></th>
                            <td>
                                <input type="submit" name="btnsave" id="btnsave" value="<?php echo __( 'Update', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?>" class="button button-primary">
                            </td>
                        </tr>
                        
                    </table>        
                </form>
            </td>
        </tr>
        </tbody>
    </table>
</div><!-- .wrap -->