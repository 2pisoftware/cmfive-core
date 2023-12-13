<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach ($time_entries as $date => $entry_struct) : ?>
            <div style="padding-bottom: 20px;">
                <h4 style='border-bottom: 1px solid #777;'><?php echo $date ?> <span style='float: right;'><?php echo TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']) ?></span></h4>
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th class=" column-time">From</th>
                            <th class="column-time">To</th>
                            <th class="column-object">Task</th>
                            <th class="column-description">Description</th>
                            <th class="column-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entry_struct['entries'] as $time_entry) : ?>
                            <tr>
                                <td><?php echo formatDate($time_entry->dt_start, "H:i:s") ?></td>
                                <td><?php echo formatDate($time_entry->dt_end, "H:i:s") ?></td>
                                <td><?php echo $time_entry->getLinkedObject() ? get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink() : '' ?></td>
                                <td><?php echo $time_entry->getComment()->comment ?></td>
                                <td>

                                    <?php
                                    echo HtmlBootstrap5::buttonGroup(
                                        ($time_entry->object_class == 'Task' ? HtmlBootstrap5::b('/task/edit/' . $time_entry->object_id . "#timelog", "View All", null, null, false, "btn btn-primary") : '') .
                                            ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::box('/timelog/edit/' . $time_entry->id, "Edit", true, null, null, null, "isbox", null, "btn btn-secondary") : '') .
                                            ($time_entry->canEdit(AuthService::getInstance($w)->user()) || $time_entry->canDelete(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::dropdownButton(
                                                "More",
                                                [
                                                    ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::box('/timelog/move/' . $time_entry->id, 'Move', true, null, null, null, "isbox", null, "dropdown-item btn-sm text-start") : ''),
                                                    '<hr class="dropdown-divider">',
                                                    ($time_entry->canDelete(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::b('/timelog/delete/' . $time_entry->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message, null, false, "dropdown-item btn-sm text-start") : '')
                                                ],
                                                "btn-info btn btn-sm rounded-0 rounded-end-1"
                                            ) : '')
                                    ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
