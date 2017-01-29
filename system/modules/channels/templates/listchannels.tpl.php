<div class='row small-12 medium-6 left'>Add a New Channel: <?php echo Html::select("add_channel", array(array("Email", "email"),array("Web","web"))); ?></div>


<?php if (!empty($channels)) : ?>

    <table class="tablesorter">
        <thead>
            <tr><th>ID</th><th>Type</th><th>Name</th><th>ON/OFF</th><th>Notify Email</th><th>Notify User</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($channels as $c) : ?>
                <?php $base_channel = $c->getChannel(); ?>
                <?php if (!empty($base_channel->id)) : ?>
                    <tr>
                        <td><?php echo $base_channel->id; ?></td>
                        <td><?php echo $c->_channeltype; // get_class($c);  ?></td>
                        <td><?php echo $base_channel->name; ?></td>
                        <td><?php echo $base_channel->is_active ? "ON" : "OFF"; ?></td>
                        <td><?php echo $base_channel->notify_user_email; ?></td>
                        <td><?php $user = $base_channel->getNotifyUser();
                            echo (!empty($user->id) ? $user->getFullName() : ""); ?>
                        <td>
                            <?php echo Html::box("/channels-{$c->_channeltype}/edit/{$base_channel->id}", "Edit"); ?>
                            <?php echo Html::a("/channels-{$c->_channeltype}/delete/{$base_channel->id}", "Delete", null, null, "Are you sure you want to delete " . (!empty($base_channel->name) ? addslashes($base_channel->name) : "this channel") . "?"); ?>
                            <?php echo Html::a("/channels/listmessages/{$base_channel->id}", "Messages"); ?>
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