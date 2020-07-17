<?php echo Html::box("/channels-web/edit", "Add Web Channel", true); ?>
<?php echo Html::box("/channels-email/edit", "Add Email Channel", true); ?>

<?php if (!empty($channels)) : ?>
    <table class="tablesorter">
        <thead>
            <tr><th>ID</th><th>Type</th><th>Name</th><th>Active</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($channels as $c) : ?>
                <?php $base_channel = $c->getChannel(); ?>
                <?php if (!empty($base_channel->id)) : ?>
                    <tr>
                        <td><?php echo $base_channel->id; ?></td>
                        <td><?php echo $c->_channeltype; ?></td>
                        <td><?php echo $base_channel->name; ?></td>
                        <td><?php echo $base_channel->is_active ? "Yes" : "No"; ?></td>
                        <td>
                            <?php echo Html::box("/channels-{$c->_channeltype}/edit/{$base_channel->id}", "Edit", true); ?>
                            <?php echo Html::b("/channels-{$c->_channeltype}/delete/{$base_channel->id}", "Delete", "Are you sure you want to delete " . (!empty($base_channel->name) ? 'the ' . addslashes($base_channel->name) . ' channel' : "this channel") . "?"); ?>
                            <?php echo Html::box("/channels/listmessages/{$base_channel->id}", "Messages", true); ?>
                            <?php if ($c->_channeltype == 'email') {
                                echo Html::box("/channels-email/test/{$base_channel->id}", 'Test Connection', true);
                            } ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
