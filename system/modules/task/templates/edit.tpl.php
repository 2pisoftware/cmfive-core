<?php if (!empty($task->id)) : ?>
    <div class="row-fluid clearfix panel">
        <h3>Task [<?php echo $task->id; ?>]: <?php echo $task->title; ?></h3>
        <blockquote>
            Created: <?php echo $createdDate; ?><br />
            Taskgroup: <?php echo $task->getTaskGroupTypeTitle(); ?>
        </blockquote>
    </div>
<?php endif; ?>

<?php if (!empty($taskbanners)) : ?>
    <?php echo $taskbanners; ?>
<?php endif; ?>

<div class="tabs">
    <div class="tab-head">
        <a href="#details">Task Details</a>
        <?php if (!empty($task->id)) : ?>
            <?php if (AuthService::getInstance($w)->user()->hasRole('timelog_user')) : ?>
                <a href="#timelog">Time Log <span class='label secondary round cmfive__tab-label cmfive__count-timelog'></span></a>
            <?php endif; ?>
            <a href="#internal_comments">Internal Comments <span class='label secondary round cmfive__tab-label cmfive__count-internal_comment_section'></span></a>
            <a href="#external_comments">External Comments <span class='label secondary round cmfive__tab-label cmfive__count-external_comment_section'></span></a>
            <a href="#attachments">Attachments <span class='label secondary round cmfive__tab-label cmfive__count-attachment'></span></a>

            <?php
            $tab_headers = $w->callHook('core_template', 'tab_headers', $task);
            if (!empty($tab_headers)) {
                echo implode('', $tab_headers);
            }
            ?>
        <?php endif; ?>
    </div>
    <div class="tab-body">
        <div id="details" class="clearfix">
            <div class="row-fluid clearfix">
                <div class="row-fluid columns">
                    <?php
                    if (!empty($task->id)) {
                        echo FavoriteService::getInstance($w)->getFavoriteButton($task);
                        // Note the extra buttons only show when the task_type object
                        $tasktypeobject = $task->getTaskTypeObject();
                        echo !empty($tasktypeobject) && method_exists($tasktypeobject, "displayExtraButtons") ? $tasktypeobject->displayExtraButtons($task) : null;
                        echo $task->canDelete(AuthService::getInstance($w)->user()) ? Html::b($task->w->localUrl('/task/delete/' . $task->id), "Delete", "Are you sure you want to delete this task?", null, false, 'warning') : '';
                        echo Html::b($task->w->localURL('task/duplicatetask/' . $task->id), "Duplicate Task");
                        echo Html::b($task->w->localURL('/task/edit/?gid=' . $task->task_group_id), "New Task");

                        /** @var TaskGroup */
                        $task_group = TaskService::getInstance($w)->getTaskGroup($task->task_group_id);
                        if (!empty($task_group) && $task_group->getCanICreate()) {
                            echo Html::box("/task-group/moveTaskgroup/" . $task->id, "Move to Taskgroup", true, false, null, null, null, null, 'secondary');
                        }

                        // Extra buttons for task
                        $buttons = $w->callHook("task", "extra_buttons", $task);
                        if (!empty($buttons) && is_array($buttons)) {
                            echo implode('', $buttons);
                        }

                        echo $w->partial('listTags', ['object' => $task], 'tag');
                    }
                    ?>
                </div>
                <div class="row-fluid clearfix">
                    <div class="small-12 large-9">
                        <?php echo $form; ?>
                    </div>

                    <div class="small-12 large-3 right" style="margin-top: 16px;">
                        <?php
                        // Call hook and filter out empty/false values
                        if (!empty($task->id)) : ?>
                            <div class='row-fluid panel clearfix' id='task_subscribers'>
                                <table class="small-12 columns">
                                    <tbody>
                                        <tr>
                                            <td class="section" colspan="1">Subscribers <br> <?php echo Html::box('/task-subscriber/add/' . $task->id, 'Add', true, false, null, null, 'isbox', null, 'info center'); ?></td>
                                        </tr>
                                        <?php if (!empty($subscribers)) : ?>
                                            <?php foreach ($subscribers as $subscriber) : ?>
                                                <?php $subscriber_user = $subscriber->getUser(); ?>
                                                <?php if (!empty($subscriber_user)) : ?>
                                                    <tr <?php echo ($subscriber_user->is_external) ? 'style="background-color: #c99;"' : ''; ?>>
                                                        <td><?php echo $subscriber_user->getFullName(); ?> - <?php echo $subscriber_user->getContact()->email; ?></br>
                                                            <?php echo Html::b('/task-subscriber/delete/' . $subscriber->id, 'Delete', 'Are you sure you want to remove this subscriber?', null, false, 'warning center'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>


                            <?php $additional_details = $w->callHook('task', 'additional_details', $task);
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
                        endif;
                        ?>
                        <div class="small-12 panel" id="tasktext" style="display: none;"></div>
                        <div class="small-12 panel clearfix" id="formfields" style="display: none;"></div>
                        <div class="small-12 panel clearfix" id="formdetails" style="display: none;"></div>
                    </div>
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
</div>
<script language="javascript">
    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    var initialChange = <?php echo (empty($task->id) ? "false" : "true"); ?>;

    $(document).ready(function() {
        bindTypeChangeEvent();

        getTaskGroupData(<?php echo !empty($task->task_group_id) ? $task->task_group_id : Request::int('gid'); ?>);
        $("#task_type").trigger("change");
    });

    function selectAutocompleteCallback(event, ui) {
        if (event.target.id == "acp_task_group_id") {
            $("#formfields").hide().html("");
            $("#tasktext").hide().html("");

            getTaskGroupData(ui.item.id);
        }
    }

    function getTaskGroupData(taskgroup_id) {
        $.getJSON("/task/taskAjaxSelectbyTaskGroup/" + taskgroup_id + "/<?php echo !empty($task->id) ? $task->id : null; ?>",
            function(result) {
                if (initialChange == false) {
                    $('#task_type').parent().html(result[0]);
                    $('#task_type').val('');
                    $('#priority').parent().html(result[1]);
                    $('#assignee_id').parent().html(result[2]);
                    $('#status').html(result[4])
                }
                $('#tasktext').html(result[3]);
                $("#tasktext").fadeIn();

                bindTypeChangeEvent();
            }
        );
    }

    function bindTypeChangeEvent() {
        $("#task_type").on("change", function(event) {
            // Reset custom fields
            $("#formfields").fadeOut();
            $("#formfields").html("");

            // Get/check for extra form fields
            $.getJSON("/task/ajaxGetFieldForm/" + $("#task_type").val() + "/" + $("#task_group_id").val() + "/<?php echo !empty($task->id) ? $task->id : ''; ?>",
                function(result) {
                    if (result) {
                        $("#formfields").html(result);
                        $("#formfields").fadeIn();
                    }
                }
            );
            <?php if (!empty($task->id)) : ?>
                var task_type_value = document.getElementById("task_type").value;
                if (task_type_value.length > 0) {
                    $("#formdetails").hide();
                    $.getJSON("/task/ajaxGetExtraDetails/<?php echo $task->id; ?>/" + task_type_value, function(result) {
                        if (result[0]) {
                            $("#formdetails").html(result[0]);
                            $("#formdetails").fadeIn();
                        }
                    });
                }
            <?php endif; ?>
        });
    }

    // Submit both forms
    $("#edit_form, #form_fields_form").submit(function() {
        for (var instanceName in CKEDITOR.instances) {
            CKEDITOR.instances[instanceName].updateElement();
        }

        toggleModalLoading();
        var edit_form = {};
        var extras_form = {};
        $.each($('#edit_form').serializeArray(), function() {
            edit_form[this.name] = this.value;
        });
        $.each($('#form_fields_form').serializeArray(), function() {
            extras_form[this.name] = this.value;
        });

        var action = $(this).attr('action');
        $.ajax({
            url: action,
            type: 'POST',
            data: {
                '<?php echo \CSRF::getTokenId(); ?>': '<?php echo \CSRF::getTokenValue(); ?>',
                'edit': edit_form,
                'extra': extras_form
            },
            complete: function(response) {
                console.error(response.responseText);
                window.onbeforeunload = null;
                if ($.isNumeric(response.responseText)) {
                    window.location.href = "/task/edit/" + response.responseText;
                } else {
                    window.location.reload();
                }
            }
        });
        return false;
    });
</script>