<div class="tabs">
    <div class="tab-head">
        <a href="#members">Members</a>
    </div>
    <div class="tab-body">
        <div id="members">
            <div>
                <?php
                echo HtmlBootstrap5::b('/task/tasklist/?task_group_id=' . $taskgroup->id, 'Task List', null, null, null, "bg-primary text-light");
                echo HtmlBootstrap5::box('/task-group/addgroupmembers/' . $taskgroup->id, 'Add New Members', true, null, null, null, null, null, "bg-primary text-light");
                echo HtmlBootstrap5::box($webroot . '/task-group/viewtaskgroup/' . $taskgroup->id, 'Edit Task Group', true, null, null, null, null, null, "bg-primary text-light");
                echo HtmlBootstrap5::box($webroot . '/task-group/deletetaskgroup/' . $taskgroup->id, 'Delete Task Group', true, null, null, null, null, null, "bg-primary text-light");
                echo $viewmembers;
                ?>
            </div>

            <div class="mt-4">
                <h4>Active Tasks</h4>
                <?php echo $w->partial('listtasks', ['task_group_id' => $taskgroup->id, 'redirect' => '/task-group/viewmembergroup/' . $taskgroup->id, 'hide_filter' => true], 'task'); ?>
            </div>
        </div>
    </div>
</div>