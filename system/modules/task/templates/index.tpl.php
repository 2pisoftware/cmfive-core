<div class="card">
    <div class="card-body">
        <p class="card-text fs-4">
            You're a member of
            <span class="fw-bold">
                <?php echo count($taskgroups); ?> taskgroup<?php echo count($taskgroups) == 1 ? "" : "s"; ?>
            </span>

            with
            <span class="fw-bold">
                <?php echo Html::a($w->localUrl("/task/tasklist?task__creator_id=&task__task-group-id=&task__type=&task__priority=&task__status=&task__is-closed=0&task__dt-from=&task__dt-to=&task__filter-urgent=0&task__assignee-id="), $count_taskgroup_tasks . " task" . ($count_taskgroup_tasks == 1 ? "" : "s")) ?>
            </span>
            overall
        </p>

    </div>
</div>

<div class="row mt-3">
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body text-center d-flex align-items-center justify-content-center">
                <div class="fw-bold fs-2">
                    Assigned
                    <?php echo Html::a(
                        $w->localUrl("/task/tasklist?task__assignee-id=" . AuthService::getInstance($w)->user()->id),
                        (count($tasks) . " Task" . (count($tasks) == 1 ? "" : "s"))
                    ); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card h-100">
            <ul class="list-group list-group-flush fs-2 text-center">
                <li class="list-group-item">
                    <?php echo Html::a(
                        $w->localUrl("/task/tasklist?task__is-closed=0&task__filter-urgent=0&task__assignee-id=" . AuthService::getInstance($w)->user()->id . "&task__dt-to=" . formatDate(time(), "Y-m-d")),
                        $count_overdue . " overdue"
                    ); ?>
                </li>

                <li class="list-group-item">
                    <?php echo Html::a(
                        $w->localUrl("/task/tasklist?task__is-closed=0&task__filter-urgent=0&task__assignee-id=" . AuthService::getInstance($w)->user()->id . "&task__dt-from=" . formatDate(time(), 'Y-m-d') . "&task__dt-to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d")),
                        $count_due_soon . " due"
                    ); ?>
                    within 7 days
                </li>

                <li class="list-group-item">
                    <?php echo Html::a(
                        $w->localUrl("/task/tasklist?task__is-closed=0&task__assignee-id=" . AuthService::getInstance($w)->user()->id . "&task__dt-to=NULL&task__filter-urgent=0"),
                        $count_no_due_date
                    ) ?>
                    without a due date
                </li>
            </ul>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body text-center fs-2 d-flex align-items-center justify-content-center">
                <div>
                    <?php echo Html::a(
                        $w->localUrl("/task/tasklist?task__is-closed=0&task__assignee-id=" . AuthService::getInstance($w)->user()->id . "&task__filter-urgent=1"),
                        $count_todo_urgent . " task" . ($count_todo_urgent == 1 ? "" : "s")
                    ); ?>
                    marked <span class="fw-bold">urgent</span>
                </div>
            </div>
        </div>
    </div>
</div>