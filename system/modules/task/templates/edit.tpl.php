<?php if (!empty($task->id)) : ?>
    <div class="row-fluid clearfix panel">
        <h3>Task [<?php echo $task->id; ?>]: <?php echo $task->title; ?></h3>
        <div>
            <div>Created: <?php echo $createdDate; ?></div>
            <div>Taskgroup: <?php echo $task->getTaskGroupTypeTitle(); ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($taskbanners)) : ?>
    <?php echo $taskbanners; ?>
<?php endif; ?>

<nav class="mt-3 tabs">
    <div class="tab-head">
        <a href="#details">Task Details</a>

        <?php if (!empty($task->id)) : ?>
            <?php if (AuthService::getInstance($w)->user()->hasRole('timelog_user')) : ?>
                <a href="#timelog">Time Log</a>
            <?php endif; ?>

            <a href="#internal_comments">Internal Comments</a>
            <a href="#external_comments">External Comments</a>
            <a href="#attachments">Attachments</a>

            <?php
            $tab_headers = $w->callHook('core_template', 'tab_headers', $task);
            if (!empty($tab_headers)) {
                echo implode('', $tab_headers);
            }
            ?>
        <?php endif; ?>
    </div>

    <div class="tab-body">
        <div id="details">
            <?php
            if (!empty($task->id)) {
                echo FavoriteService::getInstance($w)->getBootstrapButton($task);

                $tasktypeobject = $task->getTaskTypeObject();
                echo !empty($tasktypeobject) && method_exists($tasktypeobject, "displayExtraButtons")
                    ? $tasktypeobject->displayExtraButtons($task)
                    : null;

                echo $task->canDelete(AuthService::getInstance($w)->user())
                    ? HtmlBootstrap5::b(
                        $task->w->localUrl('/task/delete/' . $task->id),
                        "Delete",
                        "Are you sure you want to delete this task?",
                        null,
                        false,
                        'bg-danger text-light'
                    )
                    : '';

                echo HtmlBootstrap5::b(
                    $task->w->localURL('task/duplicatetask/' . $task->id),
                    "Duplicate Task",
                    null,
                    null,
                    null,
                    "bg-secondary text-light"
                );

                echo HtmlBootstrap5::b(
                    $task->w->localURL('/task/edit/?gid=' . $task->task_group_id),
                    "New Task",
                    null,
                    null,
                    null,
                    "bg-secondary text-light"
                );

                /** @var TaskGroup */
                $task_group = TaskService::getInstance($w)->getTaskGroup($task->task_group_id);
                if (!empty($task_group) && $task_group->getCanICreate()) {
                    echo HtmlBootstrap5::box(
                        "/task-group/moveTaskgroup/" . $task->id,
                        "Move to Taskgroup",
                        true,
                        false,
                        null,
                        null,
                        null,
                        null,
                        'bg-secondary text-light'
                    );
                }

                // Extra buttons for task
                $buttons = $w->callHook("task", "extra_buttons", $task);
                if (!empty($buttons) && is_array($buttons)) {
                    echo implode('', $buttons);
                }

                echo $w->partial('listTags', ['object' => $task], 'tag');
            }
            ?>

            <div class="row">
                <div class="small-12 large-9">
                    <?php echo $form; ?>
                </div>

                <div class="small-12 large-3 right">
                    <?php if (!empty($task->id)) : ?>
                        <div class="row panel" id="task_subscribers">
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                    <p class="fs-4 m-0">Subscribers</p>
                                    <?php
                                    echo HtmlBootstrap5::box(
                                        '/task-subscriber/add/' . $task->id,
                                        'Add',
                                        true,
                                        false,
                                        null,
                                        null,
                                        'isbox',
                                        null,
                                        'bg-secondary text-light'
                                    )
                                    ?>
                                </div>
                            </div>

                            <?php if (!empty($subscribers)) : ?>
                                <style>
                                    .subscribers > div {
                                        border-bottom: 1px solid white;
                                    }

                                    .subscribers > div:last-child {
                                        border-bottom: none;
                                    }
                                </style>
                                <div class="subscribers">
                                    <?php foreach ($subscribers as $subscriber) : ?>
                                        <?php $subscriber_user = $subscriber->getUser(); ?>
                                        <?php if (!empty($subscriber_user)) : ?>
                                            <div class="p-0 d-flex justify-content-between align-items-center">
                                                <div class="d-inline-flex flex-column w-50">
                                                    <div class="pt-0"><?php echo $subscriber_user->getFullName() ?></div>
                                                    <div><?php echo $subscriber_user->getContact()->email; ?></div>
                                                </div>
                                                <?php
                                                echo HtmlBootstrap5::b(
                                                    '/task-subscriber/delete/' . $subscriber->id,
                                                    'Delete',
                                                    'Are you sure you want to remove this subscriber?',
                                                    null,
                                                    false,
                                                    'bg-warning d-inline'
                                                );
                                                ?></td>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                            <?php
                            $additional_details = $w->callHook("task", "additional_details", $task);
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
                                    <div class="row-fluid clearfix panel">
                                        <table class="small-12 columns">
                                            <tbody>
                                                <tr>
                                                    <td class="section" colspan="2">Additional Details</td>
                                                </tr>
                                                <?php foreach ($additional_details_flattened as $additional_detail) : ?>
                                                    <tr>
                                                        <td><?php echo $additional_detail[0]; ?></td>
                                                        <td><?php echo $additional_detail[1]; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif;
                            }
                            ?>
                    <?php endif; ?>

                    <div class="col panel" id="group_details" style="display: none">
                        <p>Task Group</p>
                        <table class="table table-sm">
                            <tr>
                                <th>Name</th>
                                <td id="group_name"></td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td id="group_type"></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td id="group_desc"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col panel" id="formfields" style="display: none;"></div>
                    <div class="col panel" id="formdetails" style="display: none;"></div>
                </div>
            </div>
        </div>

        <?php if (!empty($task->id)) : ?>
            <?php if (AuthService::getInstance($w)->user()->hasRole('timelog_user')) : ?>
                <div id="timelog">
                    <?php echo $w->partial("listtimelog", ["object_class" => "Task", "object_id" => $task->id, "redirect" => "task/edit/{$task->id}#timelog"], "timelog"); ?>
                </div>
            <?php endif; ?>
            <div id="internal_comments">
                <?php echo $w->partial("listcomments", ["object" => $task, "internal_only" => true, "redirect" => "task/edit/{$task->id}#internal_comments"], "admin"); ?>
            </div>
            <div id="external_comments">
                <div class='alert-box warning'>External comments may be sent to clients, exercise caution!</div>
                <?php echo $w->partial("listcomments", ["object" => $task, "internal_only" => false, "external_only" => true, "redirect" => "task/edit/{$task->id}#external_comments"], "admin"); ?>
            </div>
            <div id="attachments">
                <?php echo $w->partial("listattachments", ["object" => $task, "redirect" => "task/edit/{$task->id}#attachments"], "file"); ?>
            </div>

            <?php
            $tab_content = $w->callHook('core_template', 'tab_content', ['object' => $task, 'redirect_url' => '/task/edit/' . $task->id]);
            if (!empty($tab_content)) {
                echo implode('', $tab_content);
            }
            ?>
        <?php endif; ?>
    </div>
</nav>

<script>
    const task_id = <?php echo !empty($task->id) ? $task->id : "undefined"; ?>

    const makeSelectOptions = (select, value, label) => {
        const elem = document.createElement("option");
        elem.value = value;
        elem.innerText = label;

        select.appendChild(elem);
    };

    let controller;
    const populateTaskgroupDetails = async (value) => {
        if (controller)
            controller.abort();

        if (!value) {
            document.getElementById("group_details").style.display = "none";
            return;
        }

        controller = new AbortController();

        const json = await fetch(
            `/task/taskAjaxSelectbyTaskGroup/${value}${task_id ? `/${task_id}` : ""}`,
            { signal: controller.signal }
        )
            .then(x => x.json());

        const type = document.getElementById("task_type");
        json.types.map(x => makeSelectOptions(type, x[1], x[0]))

        const priority = document.getElementById("priority");
        json.priorities.map(x => makeSelectOptions(priority, x[1], x[0]))

        const assignee = document.getElementById("assignee_id");
        json.assignees.map(x => makeSelectOptions(assignee, x[1], x[0]))
        if (json.can_change_assignee === true) assignee.removeAttribute("disabled")

        const status = document.getElementById("status");
        json.statuses.map(x => makeSelectOptions(status, x[1], x[0]))

        document.getElementById("group_name").innerText = json.group.name;
        document.getElementById("group_type").innerText = json.group.type;
        document.getElementById("group_desc").innerText = json.group.desc;
        document.getElementById("group_details").style.display = "block";
    }

    populateTaskgroupDetails(<?php echo $task->task_group_id ?>);

    document.getElementById("task_group").addEventListener("change", async (e) => populateTaskgroupDetails(e.target.value));

    document.getElementById("edit_form").addEventListener("submit", async (e) => {
        e.preventDefault();

        const edit = [...new FormData(e.target)].reduce((obj, [key, val]) => {
            if (key === "task_group") key = "task_group_id";
            obj[`edit[${key}]`] = val;
            return obj;
        }, {
            '<?php echo \CSRF::getTokenId(); ?>': '<?php echo \CSRF::getTokenValue(); ?>'
        });

        const action = e.target.getAttribute("action");
        const res = await fetch(action, {
            method: "POST",
            body: new URLSearchParams(edit)
        });

        if (res.ok) window.location.href = `/task/edit/${await res.text()}`
        else window.location.reload();
    })
</script>