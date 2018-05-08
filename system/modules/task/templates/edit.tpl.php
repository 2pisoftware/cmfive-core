<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/vue-components/vue-resource.min.js'></script>
<script src='/system/templates/vue-components/vue-resource.min.js'></script>
<script src='/system/templates/vue-components/flatpickr/flatpickr.min.js'></script>
<script src='/system/templates/vue-components/vue-flatpickr.min.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/flatpickr/flatpickr.min.css">
<script src='/system/templates/vue-components/ckeditor5-build-classic/ckeditor.js'></script>

<div id="task_edit">
    
<div id="taskmodal" class="reveal-modal small" data-reveal data-closable>
    Are you sure you want to remove this subscriber?<br><br>
    <button class="button radius success" v-on:click="delete_subscriber">Yes</button>
    <button class="button radius alert" data-close>No</button>
</div>

<div id="delete-modal" class="reveal-modal small" data-reveal data-closable>
    Are you sure you want to delete this task?<br><br>
    <button class="button radius success" v-on:click="delete_task">Yes</button>
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
            Task title <small>Required</small>
            <input name="title" id="title" required="required" type="text" v-model="task_title">

            Due date
            <datepicker v-model="date" :config="dateconfig" placeholder="Due date"></datepicker>

            Assigned
            <model-list-select v-model="task_assignee" :list="task_assignee_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Status
            <model-list-select v-model="task_status" :list="task_status_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Priority
            <model-list-select v-model="task_priority" :list="task_priority_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Group <small>Required</small>
            <model-list-select v-model="taskgroup_id" :list="taskgroup_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            Type <small>Required</small>
            <model-list-select v-model="task_type" :list="task_type_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>

            Description
            <textarea name="description" id="description"></textarea>

            Estimated hours
            <input name="estimate_hours" id="estimate_hours" type="text" v-model="estimate_hours">
            
            Effort
            <input name="effort" id="effort" type="text" v-model="effort">

            <br>
            
            <a class="small button radius" href="/task/duplicatetask/<?php echo $task->id; ?>">Save</a>
            <a class="small button radius" href="/task/duplicatetask/<?php echo $task->id; ?>">Duplicate</a>
            <a class="small success button radius" href="/task/edit/?gid=<?php echo $task->task_group_id; ?>">New Task</a>
            <?php if ($task->canDelete($w->Auth->user())): ?>
                <button class="small alert button radius" data-reveal-id="delete-modal">Delete</button>
            <?php endif ?>
            <a class="small warning button radius" href="/task/list">Cancel</a>

            <html-segment title='Subscribers' v-if="subscribers">
                <div v-for="subscriber in subscribers" :class="{ button: true, tiny: true, radius: true, secondary: true, warning: subscriber.is_external === 0 ? true : false }">
                    {{subscriber.fullname}}
                    <a href="#" data-reveal-id="taskmodal"><i class="fa fa-times" aria-hidden="true"></i></a>
                </div>
                <a class='button tiny secondary radius' href="/task-subscriber/add/<?php echo $task->id; ?>" data-reveal-ajax="true" data-reveal-id="taskmodal"><i class="fa fa-plus" aria-hidden="true"></i></a>
            </html-segment>
            
            <html-segment title='Tags'>
                <?php echo $w->partial('listTags', ['object' => $task], 'tag'); ?>
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
            ?>
        </div>

<?php //echo Html::box('/task-subscriber/add/' . $task->id, 'Add', true, false, null, null, 'isbox', null, 'info center'); ?>
<?php //echo Html::b('/task-subscriber/delete/' . $subscriber->id, 'Delete', 'Are you sure you want to remove this subscriber?', null, false, 'warning center'); ?>
                                                   
                             
                       

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
    Vue.component("model-list-select", VueSearchSelect.ModelListSelect);
    Vue.component('datepicker', VueFlatpickr);

    new Vue({
        el: '#task_edit',
        
        data: {
            taskgroup_id: "<?php echo $t['task_group_id']; ?>",
            task_type: "<?php echo $t['task_type']; ?>",
            task_title: "<?php echo $t['title']; ?>",
            task_id: "<?php echo $t['id']; ?>",
            task_status: "<?php echo $t['status']; ?>",
            task_priority: "<?php echo $t['priority']; ?>",
            task_assignee: "<?php echo $task_assignee; ?>",
            estimate_hours: "<?php echo $task->estimate_hours; ?>",
            effort: "<?php echo $task->effort; ?>",
            description: "<?php echo $task->description; ?>",
            can_i_assign: "<?php echo $can_i_assign; ?>",
            subscribers: <?php echo $subscribers; ?>,
            
            taskgroup_list: <?php echo $taskgroup_list; ?>,
            task_type_list: <?php echo $task_type_list; ?>,
            task_status_list: <?php echo $task_status_list; ?>,
            task_priority_list: <?php echo $task_priority_list; ?>,
            task_assignee_list: <?php echo $task_assignee_list; ?>,

            date: null,
            dateconfig: {
                altFormat: "j F Y",
                altInput: true
            }
        },
                
        methods: {
            delete_task: function() {
                Vue.http.get('/task-ajax/delete', {params:{task_id: this.task_id}}).then(function (response) {
                    if (response.body.data === "deleted")
                        window.location.replace("/task/list");
                },
                function (error) {

                });
            },

            save_task: function() {
                Vue.http.get('/task-ajax/save', {params:{task_id: this.task_id}}).then(function (response) {
                    if (response.body.data === "saved"){}
                        
                },
                function (error) {

                });
            },

            delete_subscriber: function(subscriber) {
                
            }
        },

        created: function() {
            //ClassicEditor.create('description');
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
