<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach ($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $date; ?><span class='right'><?php echo TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']); ?></span></h4>
            <table class='small-12'>
                <thead>
                    <tr>
                        <th width="10%">From</th>
                        <th width="10%">To</th>
                        <th width="10%">Additionals</th>
                        <th width="20%">Object</th>
                        <th width="40%">Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entry_struct['entries'] as $time_entry) : ?>
                        <tr>
                            <td><?php echo formatDate($time_entry->dt_start, "H:i:s"); ?></td>
                            <td><?php echo formatDate($time_entry->dt_end, "H:i:s"); ?></td>

                            <td><?php
                                // Call hook and filter out empty/false values
                                if (!empty($task->id)) : ?>
                                    <div class='row-fluid panel clearfix' id='task_subscribers'>
                                        <table class="small-12 columns">
                                            <tbody>
                                                <tr>
                                                    <td class="section" colspan="1">Subscribers <br> <?php echo Html::box('/task-subscriber/add/' . $task->id, 'Add', true, false, null, null, 'isbox', null, 'info center'); ?></td>
                                                </tr>
                                                <?php if (!empty($subscribers)) : ?>
                                                    <?php foreach ($subscribers as $subscriber) : ?>
                                                        <?php $subscriber_user = $subscriber->getUser(); ?>
                                                        <?php if (!empty($subscriber_user)) : ?>
                                                            <tr <?php echo ($subscriber_user->is_external) ? 'style="background-color: #c99;"' : ''; ?>>
                                                                <td><?php echo $subscriber_user->getFullName(); ?> - <?php echo $subscriber_user->getContact()->email; ?></br>
                                                                    <?php echo Html::b('/task-subscriber/delete/' . $subscriber->id, 'Delete', 'Are you sure you want to remove this subscriber?', null, false, 'warning center'); ?></td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>


                                    <?php $additional_details = $w->callHook('task', 'additional_details', $task);
                                    if (!is_null($additional_details) && is_array($additional_details)) {
                                        $additional_details_flattened = [];
                                        foreach ($additional_details as $module_details) {
                                            if (isset($module_details[0]) && !is_array($module_details[0])) {
                                                $additional_details_flattened[] = $module_details;
                                            } else {
                                                foreach ($module_details as $details) {
                                                    $additional_details_flattened[] = $details;
                                                }
                                            }
                                        }
                                        if (!empty($additional_details_flattened)) : ?>
                        <tr>
                            <?php foreach ($additional_details_flattened as $additional_detail) : ?>
                                <td><?php echo $additional_detail[0] . ':' . $additional_detail[1]; ?></td>
                            <?php endforeach; ?>
                        </tr>
            <?php endif;
                                    }
                                endif;
            ?></td>
            <td><?php echo ($time_entry->getLinkedObject() ? get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink() : ''); ?></td>
            <td>
                <pre class="break-pre" style="font-family: sans-serif;"><?php echo $time_entry->getComment()->comment; ?></pre>
            </td>
            <td>
                <?php echo $time_entry->object_class == "Task" ? Html::b('/task/edit/' . $time_entry->object_id . "#timelog", "View Time Log") : ""; ?>
                <?php echo $time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/edit/' . $time_entry->id, 'Edit', true) : ''; ?>
                <?php echo $time_entry->canEdit(AuthService::getInstance($w)->user()) ? Html::box('/timelog/move/' . $time_entry->id, 'Move', true) : ''; ?>
                <?php $confirmation_message = implode("", $w->callHook("timelog", "before_display_timelog", $time_entry)); ?>
                <?php echo $time_entry->canDelete(AuthService::getInstance($w)->user()) ? Html::b('/timelog/delete/' . $time_entry->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message) : ''; ?>
            </td>
            </tr>
        <?php endforeach; ?>
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
