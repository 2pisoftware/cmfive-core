<style>
    #task_edit_modal > .close-reveal-modal {
        color: #2A2869;
    }
</style>

<div id="task_view_<?php echo $task->id; ?>" v-on:editTask="editTask()">
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <h1 class="<?php echo $task->isOverdue() ? 'task_overdue' : ''; ?>"><?php echo $task->getSelectOptionTitle(); ?></h1>
            <h4 style="color: #C61213;" class='display-heading'>Assigned <strong><?php echo $task->priority ?> Priority</strong> to <strong><?php echo $task->getAssignee()->getFullName(); ?></strong> and due <strong><?php echo $task->dt_due; ?></strong></h4>
            <h4 class='display-heading'>Description</h4>
            <div class='display-content' v-html="task_description"></div>
            <br>
            <button style="background-color: #2A2869;" class='button expand' v-on:click.prevent="editTask()">Edit</button>
            <br><br>
            <html-segment title='Task group'><?php echo $task->getTaskGroup()->title; ?></html-segment>
            <html-segment title='Subscribers'>
                <?php foreach($task->getSubscribers() as $subscriber): ?>
                    <button class='button tiny secondary radius disabled'>
                        <?php echo $subscriber->getUser()->getFullName(); ?>
                    </button>
                <?php endforeach; ?>
                <button class='button tiny secondary radius'>
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </html-segment>
            <html-segment title='Tags'>
                <?php echo $w->partial('listTags', ['object' => $task], 'tag'); ?>
            </html-segment>
        </div>
    </div>
</div>

<div id="task_edit_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
</div>

<script>
    var task_view_<?php echo $task->id; ?> = new Vue({
        el: '#task_view_<?php echo $task->id; ?>',
        data: function() {
            return {
                task: <?php echo json_encode($task->toArray()); ?>
            };
        },
        computed: {
            task_description: function() {
                return this.task.description ? this.task.description : 'No description';
            }
        },
        methods: {
            editTask: function() {
                $("#task_edit_modal").foundation('reveal', 'open', '/task/edit/<?php echo $task->id; ?>');
            }
        }
    });
</script>