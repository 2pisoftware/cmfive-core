<?php if (!empty($attachments)) : ?>
    <ul class='small-block-grid-12 medium-block-grid-4'>
        <?php foreach ($attachments as $attachment_adapter => $sorted_attachments) : ?>
            <li class='text-center'>
                <div style='background-color: <?php echo Config::get('file.adapters.' . $attachment_adapter . '.active') == true ? '#008CBA' : '#ff4742'; ?>; border: 1px solid #ccc; padding: 20px;'>
                    <h3 style='text-transform: uppercase;'><?php echo $attachment_adapter; ?> <?php echo Config::get('file.adapters.' . $attachment_adapter . '.active') == false ? '(inactive)' : ''; ?></h3>
                    <h4><?php echo count($sorted_attachments); ?> attachment<?php echo count($sorted_attachments) == 1 ? '' : 's'; ?></h4>

                    <?php if (count($sorted_attachments) > 0) {
                        foreach ($attachments as $_adapter => $_att) {
                            if ($_adapter !== $attachment_adapter && Config::get('file.adapters.' . $_adapter . '.active') == true) {
                                echo HtmlBootstrap5::b('/file-admin/moveToAdapter?from_adapter=' . $attachment_adapter . '&to_adapter=' . $_adapter, 'Move all to ' . strtoupper($_adapter), 'Are you sure you want to move all attachments from ' . $attachment_adapter . ' to ' . $_adapter, null, false, 'secondary expand');
                            }
                        }
                    } ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <h3>No attachments available</h3>
<?php endif;
