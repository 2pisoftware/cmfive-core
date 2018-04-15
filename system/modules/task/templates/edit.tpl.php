<div id="task_edit">
    
<div id="taskmodal" class="reveal-modal small" data-reveal>
    Are you sure you want to remove this subscriber?<br><br>
    <button class="button radius success" v-on:click="deleteSubscriber">Yes</button>
    <button class="button radius alert" data-close>No</button>
</div>
    
<div class='row-fluid'>
<div class='small-12 columns'>
    <h3><?php echo $task->title?></h3><?php echo $w->Favorite->getFavoriteButton($task); ?>
    Created: <?php // echo $createdDate; ?><br>
    Taskgroup: <?php echo $task->getTaskGroupTypeTitle(); ?>
    <br><br>
<html-tabs>
    <html-tab title='Details' icon='' :selected="true">
        <div class="row-fluid columns">
            Group <small>Required</small>
            <model-list-select v-model="taskgroup_id" :list="taskgroup_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            Type <small>Required</small>
            <model-list-select v-model="task_type" :list="task_type_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Title <small>Required</small>
            <input name="title" id="title" required="required" type="text" v-model="task_title">
           
            Status
            <model-list-select v-model="task_status" :list="task_status_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Priority
            <model-list-select v-model="task_priority" :list="task_priority_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Assigned To
            <model-list-select v-model="task_assignee" :list="task_assignee_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            Estimated hours
            <input name="estimate_hours" id="estimate_hours" type="text" v-model="estimate_hours">
            
            Effort
            <input name="effort" id="effort" type="text" v-model="effort">
            
            Description
            <textarea id="description" name="description" v-model="description"></textarea>
            <br>
            <?php if ($task->canDelete($w->Auth->user())): ?>
                <a class="small alert button radius" href="/task/delete/<?php echo $task->id; ?>">Delete Task</a>
            <?php endif ?>
            
            <a class="small button radius" href="/task/duplicatetask/<?php echo $task->id; ?>">Duplicate Task</a>
            <a class="small success button radius" href="/task/edit/?gid=<?php echo $task->task_group_id; ?>">New Task</a>
            <a class="small warning button radius" href="/task-group/moveTaskgroup/<?php echo $task->id; ?>">Move to Taskgroup</a>
            
            <html-segment title='Subscribers'>
                <?php foreach($task->getSubscribers() as $subscriber): ?>
                    <div class='button tiny secondary radius <?php echo $subscriber->getUser()->is_external ? 'warning' : ''; ?>'>
                        <?php echo $subscriber->getUser()->getFullName(); ?> - <?php echo $subscriber->getUser()->getContact()->email; ?>
                        <a href="#" data-reveal-id="taskmodal"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </div>
                <?php endforeach; ?>
                    <a class='button tiny secondary radius' href="/task-subscriber/add/<?php echo $task->id; ?>" data-reveal-ajax="true" data-reveal-id="taskmodal"><i class="fa fa-plus" aria-hidden="true"></i></a>
            </html-segment>
            
            
            
            
            <?php 
                echo $w->Favorite->getFavoriteButton($task);
                // Note the extra buttons only show when the task_type object
                $tasktypeobject = $task->getTaskTypeObject();
                echo !empty($tasktypeobject) ? $tasktypeobject->displayExtraButtons($task) : null; 
         
                   // "Delete", "Are you sure you want to delete this task?", null, false, 'warning') 
                   
                // Extra buttons for task
                $buttons = $w->callHook("task", "extra_buttons", $task);
                if (!empty($buttons) && is_array($buttons)) {
                        echo implode('', $buttons);
                }

                echo $w->partial('listTags', ['object' => $task], 'tag');
            ?>
        </div>

             
<?php echo Html::box('/task-subscriber/add/' . $task->id, 'Add', true, false, null, null, 'isbox', null, 'info center'); ?>
<?php echo Html::b('/task-subscriber/delete/' . $subscriber->id, 'Delete', 'Are you sure you want to remove this subscriber?', null, false, 'warning center'); ?>
                                                   
                             
                       

                        <?php $additional_details = $w->callHook('task', 'additional_details', $task);
                        if (!is_null($additional_details) && is_array($additional_details)) {
                                $additional_details = array_values(array_filter($additional_details ? : []));
                                if (count($additional_details) > 0) : ?>
                                        <div class="row-fluid clearfix panel">
                                                <table class="small-12 columns">
                                                        <tbody>
                                                                <tr><td class="section" colspan="2">Additional Details</td></tr>
                                                                <?php foreach($additional_details as $additional_detail) : ?>
                                                                        <tr><td><?php echo $additional_detail[0]; ?></td><td><?php echo $additional_detail[1]; ?></td></tr>
                                                                <?php endforeach; ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                <?php endif;
                        }
                
        ?>
            
    </html-tab>
    <html-tab title='Time Log' icon='fa-clock'>
        <?php echo $w->partial("listtimelog", ["object_class" => "Task", "object_id" => $task->id, "redirect" => "task/edit/{$task->id}#timelog"], "timelog"); ?>
    </html-tab>
    <html-tab title='Internal Comments' icon='fa-comments'>
        <?php echo $w->partial("listcomments",array("object" => $task, "internal_only" => true, "redirect" => "task/edit/{$task->id}#internal_comments"), "admin"); ?>
    </html-tab>
    <html-tab title='External Comments' icon='fa-comments'>
        <div class='alert-box warning'>External comments may be sent to clients, exercise caution!</div>
        <?php echo $w->partial("listcomments",array("object" => $task, "internal_only" => false, "external_only" => true, "redirect" => "task/edit/{$task->id}#external_comments"), "admin"); ?>
    </html-tab>
    <html-tab title='Attachments' icon='fa-file'>
        <?php echo $w->partial("listattachments",array("object" => $task, "redirect" => "task/edit/{$task->id}#attachments"), "file"); ?>
    </html-tab>
    <?php if ($task->getCanINotify()):?>
        <html-tab title='Notifications' icon=''>
            <div class="row small-12">
                <h4>If you do not set notifications for this Task then the default settings for this Task group will be used</h4>
            </div>
            <?php // echo $tasknotify;?>
        </html-tab>
    <?php endif;?>
</html-tabs>
    </div>
        </div>
</div>
</div>
</div>

<script>
    new Vue({
        el: '#task_edit',
        
        data: {
            taskgroup_id: "<?php echo $t['task_group_id']; ?>",
            task_type: "<?php echo $t['task_type']; ?>",
            task_title: "<?php echo $t['title']; ?>",
            task_status: "<?php echo $t['status']; ?>",
            task_priority: "<?php echo $t['priority']; ?>",
            task_assignee: "<?php echo $task_assignee; ?>",
            estimate_hours: "<?php echo $task->estimate_hours; ?>",
            effort: "<?php echo $task->effort; ?>",
            description: "<?php echo $task->description; ?>",
            can_i_assign: "<?php echo $can_i_assign; ?>",
            
            taskgroup_list: <?php echo $taskgroup_list; ?>,
            task_type_list: <?php echo $task_type_list; ?>,
            task_status_list: <?php echo $task_status_list; ?>,
            task_priority_list: <?php echo $task_priority_list; ?>,
            task_assignee_list: <?php echo $task_assignee_list; ?>
        },
                
        methods: {
            deleteSubscriber: function(subscriber) {
                
            }
        }
    });
    
    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    var initialChange = <?php echo (empty($task->id) ? "false" : "true"); ?>;

    $(document).ready(function() {
        bindTypeChangeEvent();

        getTaskGroupData(<?php echo !empty($task->task_group_id) ? $task->task_group_id : $w->request('gid'); ?>);
        $("#task_type").trigger("change");
        
        console.log("<?php echo $t['task_group_id']; ?>");
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
                    $('#priority').parent().html(result[1]);
                    $('#assignee_id').parent().html(result[2]);
                    $('#status').html(result[4])
                }
                initialChange = true;
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
        for(var instanceName in CKEDITOR.instances) {
            CKEDITOR.instances[instanceName].updateElement();
        }
        
        toggleModalLoading();
        var edit_form = {};
        var extras_form = {};
        $.each($('#edit_form').serializeArray(), function(){
            edit_form[this.name] = this.value;
        });
        $.each($('#form_fields_form').serializeArray(), function(){
            extras_form[this.name] = this.value;
        });
        
        var action = $(this).attr('action');
        $.ajax({
            url  : action,
            type : 'POST',
            data : {
                '<?php echo \CSRF::getTokenId(); ?>': '<?php echo \CSRF::getTokenValue(); ?>', 
                'edit': edit_form, 
                'extra': extras_form
            },
            complete: function(response) {
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
