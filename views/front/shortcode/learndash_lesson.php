<?php
if( is_array($recordings) ) {
    
    foreach( $recordings as $recording ) {
?>
<p class="has-large-font-size"><?php echo $recording->title?></p>
<iframe loading="lazy" src="<?php echo $recording->embed_code?>" width="1020" height="574" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen=""></iframe>
<?php 
    }
}
?>