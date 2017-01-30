<?php // echo $w->partial("listtaskgroups", array("taskgroups" => $taskgroups, "redirect" => "/tasks", "should_filter" => true, "filter_closed_tasks" => true), "task"); ?>

<div class="row-fluid show-for-large-up">
    <div class="small-12 columns panel">
        <h4>
            <?php _e("You're a member of"); ?> <b><?php echo count($taskgroups); ?> <?php  echo _n('taskgroup','taskgroups',count($taskgroups)); ?></b><br/>
            <?php _e('With'); ?> <b><?php echo Html::a($w->localUrl("/task/tasklist"), $count_taskgroup_tasks . " "._n("task","tasks",$count_taskgroup_tasks)); ?></b> overall
        </h4>
    </div>
</div>

<div class="row-fluid show-for-large-up" data-equalizer>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center"><?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id), count($tasks) . " ". _n("Task","Tasks",count($tasks))); ?> <small><?php _e('assigned to you'); ?></small></h2>
        </div>
    </div>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <?php if ($count_overdue > 0) : ?>
            <h2 class="text-center"><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=" . formatDate(time(), "Y-m-d"), $count_overdue . __(" overdue")); ?></h2>
            <?php endif; ?>
            <?php if ($count_due_soon == 0) : ?>
                <h4 class='text-center'><b><?php _e('0 due'); ?> </b><?php _e('within 7 days'); ?></h4>
            <?php else: ?>
                <h4 class="text-center"><b><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_from=" . formatDate(time(), 'Y-m-d') . "&dt_to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d"), $count_due_soon . __(" due")); ?></b><?php _e('within 7 days'); ?></h4>
            <?php endif; ?>
            <?php if ($count_no_due_date > 0) : ?>
                <hr style="margin: 5px 0px;"/>
                <p class="text-center"><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=NULL", $count_no_due_date); ?> <?php _e('without a due date'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center">
                <?php if ($count_todo_urgent == 0) : ?>
                    <?php echo $count_todo_urgent; ?> <?php echo _n('task','tasks',$count_todo_urgent); ?> <?php _e('marked'); ?> <strong><?php _e('urgent'); ?></strong>
                <?php else : ?>
                    <?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&task_priority=Urgent"), $count_todo_urgent . _n('task','tasks',$count_todo_urgent)); ?> <?php _e('marked'); ?> <strong><?php _e('urgent'); ?></strong>
                <?php endif; ?>
            </h2>
        </div>
    </div>
</div>

<div class="row-fluid show-for-medium-down">
    <div class="row-fluid panel clearfix">
        <div class="small-12 medium-6 columns text-center"><?php _e("You're a member of "); ?> <b><?php echo count($taskgroups); ?> <? echo _n('taskgroup','taskgroups',count($taskgroups)); ?> </b></div>
        <div class="small-12 medium-6 columns text-center"><?php _e('With'); ?> <b><?php echo Html::a($w->localUrl("/task/tasklist"), $count_taskgroup_tasks . _n('task','tasks',$count_taskgroup_tasks)); ?></b> <?php _e('overall'); ?></div>
    </div>
    <div class="row-fluid clearfix panel">
        <div class="small-12 columns text-center"><?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id), (count($tasks) . _n("Task","Tasks",count($tasks)))); ?> <?php _e('assigned to you'); ?></div>
        <div class="small-12">
            <div class="row-fluid clearfix">
                <?php if ($count_overdue > 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><strong><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=" . formatDate(time(), "Y-m-d"), $count_overdue . __(" overdue")); ?></strong></div>
                <?php endif; ?>
                <?php if ($count_due_soon == 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><b><?php _e('0 due'); ?></b> <?php _e('with 7 days'); ?></div>
                <?php else: ?>
                    <div class="small-12 medium-6 columns text-center"><b><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_from=" . formatDate(time(), 'Y-m-d') . "&dt_to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d"), $count_due_soon . __(" due")); ?></b> <?php _e('within 7 days'); ?></div>
                <?php endif; ?>
                <?php if ($count_no_due_date > 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=NULL", $count_no_due_date); ?> <?php _e('without a due date'); ?></div>
                <?php endif; ?>
                <?php if ($count_todo_urgent == 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo $count_todo_urgent; ?> <?php echo _n('Task','Tasks',$count_todo_urgent); ?> <?php _e('marked'); ?> <strong><?php _e('urgent'); ?></strong></div>
                <?php else : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&task_priority=Urgent"), $count_todo_urgent . " task" . _n("task","tasks",$count_todo_urgent)); ?> <?php _e('marked'); ?> <strong><?php _e('urgent'); ?></strong></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
