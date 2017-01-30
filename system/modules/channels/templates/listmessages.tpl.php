<?php if (!empty($messages)) : ?>

    <table class="tablesorter">
        <thead><tr><th><?php _e('ID'); ?></th><th><?php _e('Type'); ?></th><th><?php _e('Channel'); ?></th><th><?php _e('Failed Processes'); ?></th><th><?php _e('Run Time'); ?></th><th><?php _e('Actions'); ?></th></tr></thead>
        <tbody>
            <?php foreach ($messages as $m) : ?>
                <?php $channel = $m->getChannel(); ?>
                <tr>
                    <td><?php echo $m->id; ?></td>
                    <td><?php echo $m->message_type; ?></td>
                    <td><?php echo (!empty($channel->id) ? $channel->name : ""); ?></td>
                    <td><?php echo $m->getFailedProcesses(); ?></td>
                    <td><?php echo formatDateTime($m->dt_created); ?></td>
                    <td><?php echo Html::a("/channels/listmessagestatuses/{$m->id}", __("View Message Statuses")); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <p><?php _e('No messages found.'); ?></p>

<?php endif; ?>
