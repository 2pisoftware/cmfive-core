<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach ($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $date ?> <span style='float: right;'><?php echo TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']) ?></span></h4>
            <table class="table-striped">
                <thead>
                    <tr>
                        <th class="column-time">From</th>
                        <th class="column-time">To</th>
                        <th class="column-object">Object</th>
                        <th class="column-description">Description</th>
                        <th class="column-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = [];
                    foreach ($entry_struct['entries'] as $time_entry) {
                        $data[] = [
                            formatDate($time_entry->dt_start, "H:i:s"),
                            formatDate($time_entry->dt_end, "H:i:s"),
                            $time_entry->getLinkedObject() ? get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink() : '',
                            $time_entry->getComment()->comment,
                            ($time_entry->object_class == "Task" ? Html::b('/task/edit/' . $time_entry->object_id . "#timelog", "View Time Log") : "") .
                                ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/edit/' . $time_entry->id, 'Edit', true) : "") .
                                ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/move/' . $time_entry->id, 'Move', true) : "") .
                                ($time_entry->canDelete(AuthService::getInstance($w)->user()) ? Html::b('/timelog/delete/' . $time_entry->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message) : "")
                        ];
                    }
                    echo HtmlBootstrap5::table($data, null, "table-striped");
                    ?>
                </tbody>
            </table>
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
