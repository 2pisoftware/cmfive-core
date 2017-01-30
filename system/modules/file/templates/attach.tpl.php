<form action="<?php echo WEBROOT; ?>/file/attach" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
    <table class="form" width="100%">
        <tr><td class='section' colspan='2'><?php _e('Attache a File'); ?></td></tr>
        <?php if (!empty($types) && sizeof($types)) : ?>
            <tr><td nowrap='true'><?php _e('Attachment Type'); ?></td><td><?php echo Html::select("type_code",$types); ?></td></tr>
        <?php endif; ?>
        <tr><td nowrap='true'><?php _e('File'); ?></td><td><input type="file" name="file" /></td></tr>
        <tr><td nowrap='true'><?php _e('Title'); ?></td><td><input type="text" name="title" /></td></tr>
        <tr><td colspan='2'><?php _e('Description'); ?></td></tr>
        <tr><td colspan='2'><textarea name="description" cols="30" rows="5"></textarea></td></tr>
        <tr><td colspan='2'><input type="submit" name="" value="<?php _e('Upload'); ?>"/></td></tr>

        <input type="hidden" name="table" value="<?php echo !empty($table) ? $table : ''; ?>"/>
        <input type="hidden" name="id" value="<?php echo !empty($id) ? $id : ''; ?>"/>
        <input type="hidden" name="url" value="<?php echo !empty($url) ? $url : ''; ?>"/>

    </table>
</form>
