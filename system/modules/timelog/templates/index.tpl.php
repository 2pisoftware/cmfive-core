<div class='row'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach ($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;' class="mt-2">
                <?php echo $date; ?>
                <div style="float: right">
                    <?php echo TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']); ?>
                </div>
            </h4>
            <?php
                $header = ["From", "To", "Object", "Description", "Actions"];
                echo HtmlBootstrap5::table(array_map(function ($val) use ($w) {
                    $row = [
                        formatDate($val->dt_start, "H:i:s"),
                        formatDate($val->dt_end, "H:i:s"),
                        class_exists($val->object_class) ? ($val->getLinkedObject() ? get_class(object: $val->getLinkedObject()) . ": " . $val->getLinkedObject()->toLink() : '') : 'Invalid Timelog object',
                        "<pre class='break-pre text-truncate d-block mt-3' style='width: 250px;''>" . strip_tags($val->getComment()->comment) . "</pre>",
                    ];

                    $actions = [];

                    if ($val->object_class == "Task") {
                        $actions[] = HtmlBootstrap5::b('/task/edit/' . $val->object_id . "#timelog", "View Time Log", null, null, null, "btn btn-sm btn-primary");
                    }

                    if ($val->canEdit(AuthService::getInstance($w)->user())) {
                        $actions[] = HtmlBootstrap5::box('/timelog/edit/' . $val->id, 'Edit', true, null, null, null, "isbox", null, "btn btn-sm btn-primary");
                        $actions[] = HtmlBootstrap5::box('/timelog/move/' . $val->id, 'Move', true, null, null, null, "isbox", null, "btn btn-sm btn-primary");
                    }

                    if ($val->canDelete(AuthService::getInstance($w)->user())) {
                        $confirmation_message = implode("", $w->callHook("timelog", "before_display_timelog", $val));
                        $actions[] = HtmlBootstrap5::b('/timelog/delete/' . $val->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message, null, null, "btn btn-sm btn-danger");
                    }

                    $row[] = HtmlBootstrap5::buttonGroup(implode("", $actions));

                    return $row;
                }, $entry_struct["entries"]), null, "tablesorter", $header);
            ?>
        <?php endforeach; ?>
    <?php else : ?>
        <h4>No time logs found</h4>
    <?php endif; ?>
</div>

<?php if (!empty($pagination)) : ?>
    <div class="pagination-centered">
        <?php echo $pagination; ?>
    </div>
<?php endif;
