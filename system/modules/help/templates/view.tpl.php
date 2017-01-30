<ul class="breadcrumbs">
    <li><a style='font-weight: bold;' href='#' id='modal-back'><?php _e('Back'); ?></a></li>
    <li><a href="<?php echo WEBROOT . '/help/toc'; ?>"><?php _e('Home'); ?></a></li>
    <?php if (!empty($module_toc)): ?>
        <li><a href="<?php echo WEBROOT . '/help/view/' . $module_toc ?>"><?php _e('Contents'); ?></a></li>
    <?php endif; ?>
    <li><a href="<?php echo WEBROOT . '/help/view/help/onhelp'; ?>"><?php _e('Help on Help'); ?></a></li>
</ul>

<?php echo $help_content; ?>
