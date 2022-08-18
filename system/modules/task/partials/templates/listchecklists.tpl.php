<?php 

echo Html::b($task->w->localURL('/task-checklist/edit/?gid=' . $task->task_group_id), "New Checklist");
?>

<div class='row-fluid'>
    <?php if (!empty($checklists)) : ?>
        <?php foreach($checklists as $title => $checklist_items) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $title; ?><span class='right'><?php echo TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']); ?></span></h4>
            <table class='small-12'>
            <thead><tr><th width="5%"></th><th width="10%">Creation</th><th width="75%">Description</th></tr></thead>
                <tbody>
                    <?php foreach($checklist_items['entries'] as $checklist_item) : ?>
                        <tr>
                            <input type="checkbox" id="'.$checklist_item['object_id'].'" />;
                            <td><?php echo formatDate($checklist_item->c_date, "DD/MM/YYYY"); ?></td>
                            <td><?php echo $checklist_item->title; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else : ?>
        <h4>No checklists found</h4>
    <?php endif; ?>    
</div>