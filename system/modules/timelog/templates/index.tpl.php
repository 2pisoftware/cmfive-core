<div class='row-fluid'>
    <?php if (!empty($time_entries)) {
        $header = ["<span class='column-time'>From</span>", "To", "Object", "Description", "Actions"];
        foreach ($time_entries as $date => $entry_struct) {
            $data = [];
            echo "<h4 style='border-bottom: 1px solid #777;'>$date <span style='float: right;'>" . TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']) . "</span></h4>";
            foreach ($entry_struct['entries'] as $time_entry) {
                $data[] = [
                    "<span class='column-time'>" . formatDate($time_entry->dt_start, "H:i:s") . "</span>",
                    "<span class='column-time'>" . formatDate($time_entry->dt_end, "H:i:s") . "</span>",
                    "<span class='column-object'>" . $time_entry->getLinkedObject() ? get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink() . "</span>" : '',
                    "<span class='column-description'>" . $time_entry->getComment()->comment . "</span>",
                    "<span class='column-actions'>" . ($time_entry->object_class == "Task" ? Html::b('/task/edit/' . $time_entry->object_id . "#timelog", "View Time Log") : "") .
                        ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/edit/' . $time_entry->id, 'Edit', true) : "") .
                        ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/move/' . $time_entry->id, 'Move', true) : "") .
                        ($time_entry->canDelete(AuthService::getInstance($w)->user()) ? Html::b('/timelog/delete/' . $time_entry->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message)
                            . "</span>" : "")
                ];
            }
            echo HtmlBootstrap5::table($data, null, "table-striped", $header);
        }
    } ?>
</div>

<?php if (!empty($pagination)) : ?>
    <div class="pagination-centered">
        <?php echo $pagination; ?>
    </div>
<?php endif;
