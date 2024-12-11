<div class="tabs">
    <div class="tab-head">
        <a href="#dashboard">Task Groups</a>
        <a href="#create">New Task Group</a>
    </div>
    <div class="tab-body">
        <div id="dashboard">
            <?php echo $dashboard; ?>
        </div>
        <div id="create" class="clearfix">
            <?php echo $creategroup; ?>
        </div>
    </div>
</div>
<script>
    // On change of task group type, get the task types and populate the select,
    // the do the same for priority
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("task_group_type").addEventListener("change", async (e) => {
            const val = e.target.value;

            const json = await fetch(`/task-group/ajaxSelectTaskGroupType/${val}`)
                .then(x => x.json());

            document.getElementById("default_task_type").outerHTML = json[0];
            document.getElementById("default_priority").outerHTML = json[1];
        })
    });
</script>