<?php echo Html::box("/channels-processor/edit", "Add Processor", true); ?>

<?php if (!empty($processors)) : ?>
    <table class="tablesorter">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Processor Class</th>
                <th>Processor Module</th>
                <th>Attached To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($processors as $p) : ?>
                <?php $channel = $p->getChannel(); ?>
                <tr>
                    <td><?php echo $p->id; ?></td>
                    <td><?php echo $p->name; ?></td>
                    <td><?php echo $p->class; ?></td>
                    <td><?php echo $p->module; ?></td>
                    <td><?php echo !empty($channel->name) ? $channel->name : ""; ?></td>
                    <td>
                        <?php
                        // Only show edit settings form if it returns something
                        $editsettings = "";
                        if (class_exists($p->class)) {
                            $class = new $p->class($w);
                            if (method_exists($class, "getSettingsForm")) {
                                $form = $class->getSettingsForm($p->settings);
                                if (!empty($form)) {
                                    $editsettings = HtmlBootstrap5::box("/channels-processor/editsettings/{$p->id}", "Settings", true, false, null, null, "isbox", null, "dropdown-item btn-sm text-start");
                                }
                            }
                        } else {
                            LogService::getInstance($w)->setLogger('CHANNEL')->error('Processor class ' . $p->class . ' not found');
                        }

                        echo HtmlBootstrap5::buttonGroup(
                            HtmlBootstrap5::box("/channels-processor/edit/{$p->id}", "Edit", true, false, null, null, "isbox", null, "btn-sm btn-secondary") .
                                HtmlBootstrap5::dropdownButton(
                                    "More",
                                    [
                                        $editsettings,
                                        $editsettings != "" ? '<hr class="dropdown-divider">' : "",
                                        HtmlBootstrap5::b("/channels-processor/delete/{$p->id}", "Delete", "Are you sure you want to delete " . (!empty($p->name) ? $p->name : "this processor") . "?", null, false, "dropdown-item btn-sm text-start text-danger")
                                    ],
                                    "btn-info btn btn-sm rounded-0 rounded-end-1"
                                )
                        );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>