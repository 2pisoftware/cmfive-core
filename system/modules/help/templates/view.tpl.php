<ul class="breadcrumbs">
    <li><a style='font-weight: bold;' href='#' id='modal-back'>Back</a></li>
    <li><a href="<?php echo WEBROOT . '/help/toc'; ?>">Home</a></li>
    <?php if (!empty($module_toc)): ?>
        <li><a href="<?php echo WEBROOT . '/help/view/' . $module_toc ?>">Contents</a></li>
    <?php endif; ?>
    <li><a href="<?php echo WEBROOT . '/help/view/help/onhelp'; ?>">Help on Help</a></li>
</ul>

<?php echo $help_content; ?>
