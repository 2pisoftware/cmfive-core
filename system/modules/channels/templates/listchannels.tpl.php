<?php echo HtmlBootstrap5::b("/channels-web/edit", "Add Web Channel", null, null, false, "btn btn-sm btn-primary"); ?>
<?php echo HtmlBootstrap5::b("/channels-email/edit", "Add Email Channel", null, null, false, "btn btn-sm btn-primary"); ?>

<?php if (!empty($channels)) : ?>
    <table class="tablesorter">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Name</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
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
                            <?php
                            echo HtmlBootstrap5::buttonGroup(
                                HtmlBootstrap5::b("/channels-{$c->_channeltype}/edit/{$base_channel->id}", "Edit", null, null, false, "btn btn-secondary") .
                                    HtmlBootstrap5::dropdownButton(
                                        "More",
                                        [
                                            HtmlBootstrap5::b("/channels/listmessages/{$base_channel->id}", "Messages", null, null, false, "dropdown-item btn-sm text-start"),
                                            ($c->_channeltype == 'email' ? HtmlBootstrap5::b("/channels-email/test/{$base_channel->id}", 'Test Connection', null, null, false, "dropdown-item btn-sm text-start") : ''),
                                            '<hr class="dropdown-divider">',
                                            HtmlBootstrap5::b("/channels-{$c->_channeltype}/delete/{$base_channel->id}", "Delete", "Are you sure you want to delete " . (!empty($base_channel->name) ? 'the ' . addslashes($base_channel->name) . ' channel' : "this channel") . "?", null, false, "dropdown-item btn-sm text-start text-danger")
                                        ],
                                        "btn-info btn btn-sm rounded-0 rounded-end-1"
                                    )
                            ); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>