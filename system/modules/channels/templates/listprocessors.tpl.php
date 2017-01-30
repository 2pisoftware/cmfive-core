<?php echo Html::box("/channels-processor/edit", __("Add Processor"), true); ?>

<?php if (!empty($processors)) : ?>

    <table class="tablesorter">
        <thead>
            <tr><th><?php _e('ID'); ?></th><th><?php _e('Name'); ?></th><th><?php _e('Processor Class'); ?></th><th><?php _e('Processor Module'); ?></th><th><?php _e('Attached to'); ?></th><th><?php _e('Actions'); ?></th></tr>
        </thead>
        <tbody>
            <?php foreach ($processors as $p) : ?>
                <?php $channel = $p->getChannel(); ?>
                <tr>
                    <td><?php echo $p->id; ?></td>
                    <td><?php echo $p->name; ?></td>
                    <td><?php echo $p->class; ?></td>
                    <td><?php echo $p->module; ?></td>
                    <td><?php echo!empty($channel->name) ? $channel->name : ""; ?></td>
                    <td>
                        <?php echo Html::box("/channels-processor/edit/{$p->id}", __("Edit")); ?>
                        <?php echo Html::a("/channels-processor/delete/{$p->id}", __("Delete"), null, null, __("Are you sure you want to delete ") . (!empty($p->name) ? $p->name : __("this processor")) . __("?")); ?>
                        <?php 
                            // Only show edit settings form if it returns something
                            $class = new $p->class($w);
                            if (method_exists($class, "getSettingsForm")) {
                                $form = $class->getSettingsForm($p->settings);
                                if (!empty($form)) {
                                    echo Html::box("/channels-processor/editsettings/{$p->id}", __("Edit Settings")); 
                                }
                            }
                        ?>
                    </td>
                </tr>	
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
