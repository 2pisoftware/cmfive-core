<ul class="breadcrumbs">
    <li><a style='font-weight: bold;' href="#" id="modal-back"><?php _e('Back'); ?></a></li>
    <li class='current'><a href="<?php echo WEBROOT; ?>/help/toc"><?php _e('Home'); ?></a></li>
    <li><a href="<?php echo WEBROOT; ?>/help/view/help/onhelp"><?php _e('Help on Help'); ?></a></li>
</ul>
<h2><?php _e('Table of Contents'); ?></h2>
<?php echo !empty($ul) ? $ul : null;
