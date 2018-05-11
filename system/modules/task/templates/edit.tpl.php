<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/vue-components/vue-resource.min.js'></script>
<script src='/system/templates/vue-components/flatpickr/flatpickr.min.js'></script>
<script src='/system/templates/vue-components/vue-flatpickr.min.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/flatpickr/flatpickr.min.css">
<script src='/system/templates/vue-components/quill/quill.min.js'></script>
<script src='/system/templates/vue-components/quill/vue2-editor.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/quill/quill.snow.css">

<div id="task_edit">
    
<div id="taskmodal" class="reveal-modal small" data-reveal data-closable>
    Are you sure you want to remove this subscriber?<br><br>
    <button class="button radius tiny success" v-on:click="delete_subscriber">Yes</button>
    <button class="button radius tiny alert" data-close>No</button>
</div>

<div id="delete-modal" class="reveal-modal small" data-reveal data-closable>
    Are you sure you want to delete this task?<br><br>
    <button class="button tiny radius success" v-on:click="delete_task">Yes</button>
    <button class="button tiny radius alert" data-close>No</button>
</div>

<div id="save-modal" class="reveal-modal small" data-reveal data-closable>
    The task was saved successfully<br><br>
    <button class="button tiny radius success" data-close>OK</button>
</div>
    
<div class='row-fluid'>
<div class='small-12 columns'>
    <h3><?php echo $task->title?></h3><?php echo $w->Favorite->getFavoriteButton($task); ?>
    Created: <?php // echo $createdDate; ?><br>
    Taskgroup: <?php echo $task->getTaskGroupTypeTitle(); ?>
    <br><br>
<html-tabs>
    <html-tab title='Details' icon='' :selected="true">
        <div class="row-fluid">
            <div class="medium-12 large-8 columns">
                Task title <small>Required</small>
                <input name="title" id="title" required="required" type="text" v-model="title">
            </div>

            <div class="medium-12 large-4 columns">
                Due date
                <datepicker v-model="date" :config="dateconfig" placeholder="Due date"></datepicker>
            </div>
        </div>

        <div class="row-fluid">
            <div class="medium-12 large-4 columns">
                Assigned
                <model-list-select v-model="assignee_id" :list="assignee_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
            <div class="medium-12 large-4 columns">
                Status
                <model-list-select v-model="status" :list="status_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
            <div class="medium-12 large-4 columns">
                Priority
                <model-list-select v-model="priority" :list="priority_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="medium-12 large-6 columns">
                Group <small>Required</small>
                <model-list-select v-model="taskgroup_id" :list="taskgroup_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
            <div class="medium-12 large-6 columns">
                Type <small>Required</small>
                <model-list-select v-model="type" :list="type_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
        </div>
        <div class="row-fluid columns">
            Description
            <vue-editor v-model="content"></vue-editor>
            <!-- <textarea name="description" id="description"></textarea> -->
        </div>
        <div class="row-fluid">
            <div class="medium-12 large-6 columns">
                Estimated hours
                <input name="estimate_hours" id="estimate_hours" type="text" v-model="estimate_hours">
            </div>
            <div class="medium-12 large-6 columns">
                Effort
                <input name="effort" id="effort" type="text" v-model="effort">
            </div>
        </div>
            <br>
            
            <a class="tiny button radius" style="background-color: #59BC3B;" @click="save">Save</a>
            <a class="tiny button radius" style="background-color: #95ACBC;" href="/task/duplicatetask/<?php echo $task->id; ?>">Duplicate</a>
            <a class="tiny button radius" style="background-color: #68C2CD;" href="/task/edit/?gid=<?php echo $task->task_group_id; ?>">New Task</a>
            <?php if ($task->canDelete($w->Auth->user())): ?>
                <button class="tiny button radius" style="background-color: #D12229;" data-reveal-id="delete-modal">Delete</button>
            <?php endif ?>
            <a class="tiny button radius" style="background-color: #FF7A13;" href="/task/list">Cancel</a>

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
                // Note the extra buttons only show when the type object
                $tasktypeobject = $task->getTaskTypeObject();
                echo !empty($tasktypeobject) ? $tasktypeobject->displayExtraButtons($task) : null; 
         
                   // "Delete", "Are you sure you want to delete this task?", null, false, 'warning') 
                   
                // Extra buttons for task
                $buttons = $w->callHook("task", "extra_buttons", $task);
                if (!empty($buttons) && is_array($buttons)) {
                        echo implode('', $buttons);
                }
            ?>
        

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
    new Vue({
        el: '#task_edit',
        
        components: {
            "vue-editor": 'vue2-editor',
            "model-list-select": VueSearchSelect.ModelListSelect,
            "datepicker": VueFlatpickr
        },
        
        data: {
            taskgroup_id: "<?php echo $t['task_group_id']; ?>",
            type: "<?php echo $t['task_type']; ?>",
            title: "<?php echo $t['title']; ?>",
            id: "<?php echo $t['id']; ?>",
            status: "<?php echo $t['status']; ?>",
            priority: "<?php echo $t['priority']; ?>",
            assignee_id: "<?php echo $assignee_id; ?>",
            estimate_hours: "<?php echo $task->estimate_hours; ?>",
            effort: "<?php echo $task->effort; ?>",
            description: "<?php echo $task->description; ?>",
            can_i_assign: "<?php echo $can_i_assign; ?>",
            subscribers: <?php echo $subscribers; ?>,
            
            taskgroup_list: <?php echo $taskgroup_list; ?>,
            type_list: <?php echo $type_list; ?>,
            status_list: <?php echo $status_list; ?>,
            priority_list: <?php echo $priority_list; ?>,
            assignee_list: <?php echo $assignee_list; ?>,

            date: null,
            dateconfig: {
                altFormat: "j F Y",
                altInput: true
            }
        },
                
        methods: {
            delete_task: function() {
                Vue.http.get('/task-ajax/delete', {params:{id: this.id}}).then(function (response) {
                    if (response.body.data === "deleted")
                        window.location.replace("/task/list");
                },
                function (error) {

                });
            },

            save: function() {
                var params = {
                    id: this.id,
                    title: this.title,
                    dt_due: this.date,
                    assignee_id: this.assignee_id,
                    status: this.status,
                    priority: this.priority,
                    task_group_id: this.taskgroup_id,
                    type: this.type,
                    description: this.description,
                    estimate_hours: this.estimate_hours,
                    effort: this.effort
                };
                Vue.http.get('/task-ajax/save', {params: params}).then(function (response) {
                    console.log(response);
                    if (response.body.data === "updated"){
                        $('#save-modal').foundation('reveal','open');
                    }
                        
                },
                function (error) {

                });
            },

            delete_subscriber: function(subscriber) {
                
            }
        },

        created: function() {
            
        }
    });
    
    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    var initialChange = <?php echo (empty($task->id) ? "false" : "true"); ?>;

    $(document).ready(function() {
        bindTypeChangeEvent();

        getTaskGroupData(<?php echo !empty($task->task_group_id) ? $task->task_group_id : $w->request('gid'); ?>);
        $("#type").trigger("change");
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
                    $('#type').parent().html(result[0]);
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
        $("#type").on("change", function(event) {
            // Reset custom fields
            $("#formfields").fadeOut();
            $("#formfields").html("");
            
            // Get/check for extra form fields
            $.getJSON("/task/ajaxGetFieldForm/" + $("#type").val() + "/" + $("#task_group_id").val() + "/<?php echo !empty($task->id) ? $task->id : ''; ?>",
                function(result) {
                    if (result) {
                        $("#formfields").html(result);
                        $("#formfields").fadeIn();
                    }
                }
            );
            <?php if (!empty($task->id)) : ?>
                var type_value = document.getElementById("type").value;
                if (type_value.length > 0) {
                    $("#formdetails").hide();
                    $.getJSON("/task/ajaxGetExtraDetails/<?php echo $task->id; ?>/" + type_value, function(result) {
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
