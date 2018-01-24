<?php // echo $w->partial("listtaskgroups", array("taskgroups" => $taskgroups, "redirect" => "/tasks", "should_filter" => true, "filter_closed_tasks" => true), "task"); ?>

<div class="row-fluid show-for-large-up">
    <div class="small-12 columns panel">
        <h4>
            You're a member of <b><?php echo count($taskgroups); ?> taskgroup<?php echo count($taskgroups) == 1 ? "" : "s"; ?></b><br/>
            With <b><?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__filter-urgent=0&task__assignee-id="), $count_taskgroup_tasks . " task" . ($count_taskgroup_tasks == 1 ? "" : "s")) ?></b> overall
        </h4>
    </div>
</div>

<div class="row-fluid show-for-large-up" data-equalizer>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center"><?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id), (count($tasks) . " Task" . (count($tasks) == 1 ? "" : "s"))); ?> <small>assigned to you</small></h2>
        </div>
    </div>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <?php if ($count_overdue > 0) : ?>
            <h2 class="text-center"><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-to=" . formatDate(time(), "Y-m-d"), $count_overdue . " overdue"); ?></h2>
            <?php endif; ?>
            <?php if ($count_due_soon == 0) : ?>
                <h4 class='text-center'><b>0 due</b> within 7 days</h4>
            <?php else: ?>
                <h4 class="text-center"><b><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-from=" . formatDate(time(), 'Y-m-d') . "&task__dt-to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d"), $count_due_soon . " due"); ?></b> within 7 days</h4>
            <?php endif; ?>
            <?php if ($count_no_due_date > 0) : ?>
                <hr style="margin: 5px 0px;"/>
                <p class="text-center"><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-to=NULL&task__filter-urgent=0", $count_no_due_date); ?> without a due date</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="small-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center">
                <?php if ($count_todo_urgent == 0) : ?>
                    <?php echo $count_todo_urgent; ?> task<?php echo $count_todo_urgent == 1 ? "" : "s"; ?> marked <strong>urgent</strong>
                <?php else : ?>
                    <?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__assignee-id=" . $w->Auth->user()->id . "&task__filter-urgent=1"), $count_todo_urgent . " task" . ($count_todo_urgent == 1 ? "" : "s")); ?> marked <strong>urgent</strong>
                <?php endif; ?>
            </h2>
        </div>
    </div>
</div>

<div class="row-fluid show-for-medium-down">
    <div class="row-fluid panel clearfix">
        <div class="small-12 medium-6 columns text-center">You're a member of <b><?php echo count($taskgroups); ?> taskgroup<?php echo count($taskgroups) == 1 ? "" : "s"; ?></b></div>
        <div class="small-12 medium-6 columns text-center">With <b><?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__filter-urgent=0&task__assignee-id="), $count_taskgroup_tasks . " task" . ($count_taskgroup_tasks == 1 ? "" : "s")) ?></b> overall</div>
    </div>
    <div class="row-fluid clearfix panel">
        <div class="small-12 columns text-center"><?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id), (count($tasks) . " Task" . (count($tasks) == 1 ? "" : "s"))); ?> assigned to you</div>
        <div class="small-12">
            <div class="row-fluid clearfix">
                <?php if ($count_overdue > 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><strong><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-to=" . formatDate(time(), "Y-m-d"), $count_overdue . " overdue"); ?></strong></div>
                <?php endif; ?>
                <?php if ($count_due_soon == 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><b>0 due</b> within 7 days</div>
                <?php else: ?>
                    <div class="small-12 medium-6 columns text-center"><b><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__filter-urgent=0&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-from=" . formatDate(time(), 'Y-m-d') . "&task__dt-to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d"), $count_due_soon . " due"); ?></b> within 7 days</div>
                <?php endif; ?>
                <?php if ($count_no_due_date > 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo Html::a("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__assignee-id=" . $w->Auth->user()->id . "&task__dt-to=NULL&task__filter-urgent=0", $count_no_due_date); ?> without a due date</div>
                <?php endif; ?>
                <?php if ($count_todo_urgent == 0) : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo $count_todo_urgent; ?> task<?php echo $count_todo_urgent == 1 ? "" : "s"; ?> marked <strong>urgent</strong></div>
                <?php else : ?>
                    <div class="small-12 medium-6 columns text-center"><?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__assignee-id=" . $w->Auth->user()->id . "&task__filter-urgent=1"), $count_todo_urgent . " task" . ($count_todo_urgent == 1 ? "" : "s")); ?> marked <strong>urgent</strong></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
