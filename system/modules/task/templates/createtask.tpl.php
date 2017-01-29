            Creating a new Task is a two step process.<br>Please complete all steps to register a Task.
            <p>
            <table border=0>
                <tr valign=top>
                    <td>
                        <?php if (empty($createtask)) : ?>
                            <p>You need to set up a Task Group first, that can be done <a href="/task-group/viewtaskgrouptypes#">here</a></p>
                        <?php else: echo $createtask;
                        endif; ?>
                    </td>
                    <td><span id="tasktext"><?php echo !empty($tasktext) ? $tasktext : ''; ?></span></td>
                </tr>
            </table>



<script language="javascript">
    $.ajaxSetup({
        cache: false
    });

    var task_url = "/task/taskAjaxSelectbyTaskGroup?id=";

    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    $(document).ready(function() {
        $("select[id='task_group_id']").trigger("change");
    });

    $("select[id='task_group_id']").on("change", function() {
        $.getJSON(
            task_url + $(this).val(),
            function(result) {
                $('#task_type').parent().html(result[0]);
                $('#priority').parent().html(result[1]);
                $('#first_assignee_id').parent().html(result[2]);
                $('#tasktext').html(result[3]);
            }
        );
    });
</script>

