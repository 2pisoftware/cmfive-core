<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/vue-components/vue-resource.min.js'></script>
<script src='/system/templates/vue-components/flatpickr/flatpickr.min.js'></script>
<script src='/system/templates/vue-components/vue-flatpickr.min.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/flatpickr/flatpickr.min.css">
<script src='/system/templates/vue-components/quill/quill.min.js'></script>
<script src='/system/templates/vue-components/quill/vue2-editor.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/quill/quill.snow.css">

<style>
    .div1 {
        width: 7em; 
        height: 7em; 
        border-radius: 50%; 
        background-color: #DEE5EB; 
        margin-left: auto; 
        margin-right: auto;
    }

    .p1 {
        line-height: 7em;
    }
</style>

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

    <div id="modal_edit" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">
    </div>
    
    <h4>Task: <?php echo $task->title?></h4>
    Created <?php echo $task->getCreatedDate(); ?> by <?php echo $task->getCreatorName(); ?> and is due by <?php echo $task->getDueDate(); ?><br>
    <br><br>
<div class='row-fluid'>
<div class='small-12 large-8 columns'>
<html-tabs>
    <html-tab title='Task details' icon='' :selected="true">
        <div class="row">
            <div class="medium-12 large-2 columns text-center">
                Assigned to<br>
                <img style="width: 6em; height: 6em; border-radius: 50%;" src='https://www.gravatar.com/avatar/<?php echo $gravatar; ?>?d=identicon&s=250' />
                <br>{{assignee_name}}
            </div>
            <div class="medium-12 large-2 columns text-center">
                Status<br>
                <div class="div1"><p class="p1">{{status}}</p></div>
            </div>
            <div class="medium-12 large-2 columns text-center">
                Priority<br>
                <div class="div1"><p class="p1">{{priority}}</p></div>
            </div>
            <div class="medium-12 large-2 columns text-center">
                Due Date<br>
                <div class="div1"><p class="p1"><?php echo $task->getDueDate(); ?></p></div>
            </div>
            <div class="medium-12 large-2 columns text-center">
                Est.Hours<br>
                <div class="div1"><p class="p1">{{estimate_hours}}</p></div>
            </div>
            <div class="medium-12 large-2 columns">
            </div>
        </div>
        
        <div class="row">
            <div class="large-12 columns">
            <br>Description<br><br>
                {{description}}
            </div>
        </div>

        <div class="row">
            <a data-reveal-ajax="true" data-reveal-id="modal_edit" href="/task/edit/<?php echo $task->id; ?>"><button style="background-color: #59BC3B;" class='tiny button radius'>Edit</button></a>
            <a class="tiny button radius" style="background-color: #95ACBC;" href="/task/duplicatetask/<?php echo $task->id; ?>">Duplicate</a>
            <a class="tiny button radius" style="background-color: #68C2CD;" href="/task/edit/?gid=<?php echo $task->task_group_id; ?>">New Task</a>
            <?php if ($task->canDelete($w->Auth->user())): ?>
                <button class="tiny button radius" style="background-color: #D12229;" data-reveal-id="delete-modal">Delete</button>
            <?php endif ?>
            <a href="/task/list"><button class="tiny button radius" style="background-color: #FF7A13;">Cancel</button></a>
        </div>
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
    <div class='small-12 large-4 columns'>
        <html-segment title='Task group details'>
            Task group <?php echo $taskgroup->title; ?><br>
            Task type <?php echo $task->task_type; ?><br>
            Description <?php echo $taskgroup->description; ?>
        </html-segment>
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
    </div>
</div>
</div>

<script>
    new Vue({
        el: '#task_edit',
        
        components: {
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
            assignee_name: "<?php echo $assignee_name; ?>",
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

            description: null,

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
</script>
