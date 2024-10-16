<?php
use Html\Form\Html5Autocomplete;
use Html\Form\InputField\Hidden;
use Html\Form\Select;

$task_groups = array_filter(TaskService::getInstance($w)->getTaskGroups(), function ($task_group) {
    return $task_group->getCanICreate();
});

?>

<form action="/task-group/saveNewTaskgroup" method="POST">
    <?php
    echo new Hidden([
        "id|name" => "old_taskgroup_id",
        "value" => $old_taskgroup->id,
    ]);

    echo new Hidden([
        "id|name" => "new_taskgroup_id",
    ]);

    echo new Hidden([
        "id|name" => "task_id",
        "value" => $task->id,
    ]);
    ?>

    <div class="row">
        <div class="col">
            <label for="taskgroup" class="form-label">
                Task Group
                <small>Required</small>
            </label>
            <?php
            echo new Html5Autocomplete([
                "id|name" => "taskgroup",
                "class" => "form-control",
                "required" => true,
                "options" => $task_groups,
                "value" => $old_taskgroup->id,
                "placeholder" => "Select a task group",
                "maxItems" => 1,
            ])
            ?>
        </div>
    </div>

    <div class="row pt-2" id="identical_taskgroup">
        <div class="col">
            <p>No additional details required</p>
            <button type="submit" class="btn btn-primary">Move</button>
        </div>
    </div>

    <div class="row pt-2 d-none" id="different_taskgroup">
        <p class="mb-0" >Choosing a different task group type requires additional information:</p>

        <div class="row mt-0">
            <div class="col">
                <label class="form-label" for="new_task_type">New Task Type (was '<?php echo $task->task_type?>')</label>
                <?php
                echo new Select([
                    "id|name" => "new_task_type",
                    "class" => "form-select",
                ])
                ?>
            </div>

            <div class="col">
                <label class="form-label" for="new_status">New Status (was '<?php echo $task->status?>')</label>
                <?php
                echo new Select([
                    "id|name" => "new_status",
                    "class" => "form-select",
                ])
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label class="form-label" for="new_priority">New Priority (was '<?php echo $task->priority?>')</label>
                <?php
                echo new Select([
                    "id|name" => "new_priority",
                    "class" => "form-select",
                ])
                ?>
            </div>

            <div class="col">
                <label class="form-label" for="new_assignee">New Assignee (was '<?php
                    $user = AuthService::getInstance($w)->getUser($task->assignee_id);
                    echo !empty($user) ? $user->getFullName() : "unassigned";?>')</label>
                <?php
                echo new Select([
                    "id|name" => "new_assignee",
                    "class" => "form-select",
                ])
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <button type="submit" class="btn btn-primary">Move</button>
            </div>
        </div>
    </div>
</form>

<script>
    const old_taskgroup_type = "<?php echo $old_taskgroup->task_group_type; ?>";

    document.getElementById("taskgroup").addEventListener("change", async (e) => {
        const taskgroup = e.target.value;

        const json = await fetch(`/task-group/ajax_getTaskgroupDetails/${taskgroup}`).then(x => x.json());

        document.getElementById("new_taskgroup_id").value = taskgroup;

        if (json.taskgroup_type_name === old_taskgroup_type) {
            document.getElementById("identical_taskgroup").classList.remove("d-none");
            document.getElementById("different_taskgroup").classList.add("d-none");
        } else {
            document.getElementById("identical_taskgroup").classList.add("d-none");
            document.getElementById("different_taskgroup").classList.remove("d-none");

            document.getElementById("new_task_type").innerHTML = json.task_types;
            document.getElementById("new_status").innerHTML = json.statuses;
            document.getElementById("new_priority").innerHTML = json.priorities;
            document.getElementById("new_assignee").innerHTML = json.assignees;
        }
    });
</script>