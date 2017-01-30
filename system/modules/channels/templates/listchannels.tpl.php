<div class='row small-12 medium-6 left'><?php _e('Add a new channel'); ?>: <?php echo Html::select("add_channel", array(array(__("Email"), "email"),array(__("Web"),"web"))); ?></div>


<?php if (!empty($channels)) : ?>

    <table class="tablesorter">
        <thead>
            <tr><th><?php _e('ID'); ?></th><th><?php _e('Type'); ?></th><th><?php _e('Name'); ?></th><th><?php _e('ON/OFF'); ?></th><th><?php _e('Notify Email'); ?></th><th><?php _e('Notify User'); ?></th><th><?php _e('Actions'); ?></th></tr>
        </thead>
        <tbody>
            <?php foreach ($channels as $c) : ?>
                <?php $base_channel = $c->getChannel(); ?>
                <?php if (!empty($base_channel->id)) : ?>
                    <tr>
                        <td><?php echo $base_channel->id; ?></td>
                        <td><?php echo $c->_channeltype; // get_class($c);  ?></td>
                        <td><?php echo $base_channel->name; ?></td>
                        <td><?php echo $base_channel->is_active ? __"ON") : __("OFF"); ?></td>
                        <td><?php echo $base_channel->notify_user_email; ?></td>
                        <td><?php $user = $base_channel->getNotifyUser();
                            echo (!empty($user->id) ? $user->getFullName() : ""); ?>
                        <td>
                            <?php echo Html::box("/channels-{$c->_channeltype}/edit/{$base_channel->id}", __("Edit")); ?>
                            <?php echo Html::a("/channels-{$c->_channeltype}/delete/{$base_channel->id}", __("Delete"), null, null, __("Are you sure you want to delete ") . (!empty($base_channel->name) ? addslashes($base_channel->name) : __("this channel")) . "?"); ?>
                            <?php echo Html::a("/channels/listmessages/{$base_channel->id}", __("Messages")); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<script type="text/javascript">

    jQuery("#add_channel").change(function(e) {
        if (e.target.selectedIndex > 0) {
            $("#cmfive-modal").foundation('reveal', 'open', "/channels-" + jQuery(this).val() + "/edit");
            e.target.selectedIndex = 0;
        }
    });

</script>
