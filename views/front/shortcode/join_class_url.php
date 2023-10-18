<?php if( !empty($join_class_url) ) { ?>
    <?php if( is_array($join_class_url) ) {?>
        <?php foreach( $join_class_url as $join_url ){ ?>
        <div class="wp-container-1 wp-block-buttons kiflayn_learndash_join_url_container">
            <div class="wp-block-button is-style-outline">
                <a class="wp-block-button__link has-white-color has-vivid-red-background-color has-text-color has-background" href="<?php echo $join_url->url;?>" target="_blank" rel="noreferrer noopener"><?php echo __( 'Join Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></a>
            </div>
        </div>
        <?php }?>
    <?php }else{ ?>
    <div class="wp-container-1 wp-block-buttons kiflayn_learndash_join_url_container">
        <div class="wp-block-button is-style-outline">
            <a class="wp-block-button__link has-white-color has-vivid-red-background-color has-text-color has-background" href="<?php echo $join_class_url->url;?>" target="_blank" rel="noreferrer noopener"><?php echo __( 'Join Live Class', KIFLAYN_LEARNDASH_TEXT_DOMAIN );?></a>
        </div>
    </div>
    <?php }?>
<?php }