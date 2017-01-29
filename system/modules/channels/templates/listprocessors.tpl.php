<?php echo Html::box("/channels-processor/edit", "Add Processor", true); ?>

<?php if (!empty($processors)) : ?>

    <table class="tablesorter">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Processor Class</th><th>Processor Module</th><th>Attached To</th><th>Actions</th></tr>
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
                        <?php echo Html::box("/channels-processor/edit/{$p->id}", "Edit"); ?>
                        <?php echo Html::a("/channels-processor/delete/{$p->id}", "Delete", null, null, "Are you sure you want to delete " . (!empty($p->name) ? $p->name : "this processor") . "?"); ?>
                        <?php 
                            // Only show edit settings form if it returns something
                            $class = new $p->class($w);
                            if (method_exists($class, "getSettingsForm")) {
                                $form = $class->getSettingsForm($p->settings);
                                if (!empty($form)) {
                                    echo Html::box("/channels-processor/editsettings/{$p->id}", "Edit Settings"); 
                                }
                            }
                        ?>
                    </td>
                </tr>	
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
