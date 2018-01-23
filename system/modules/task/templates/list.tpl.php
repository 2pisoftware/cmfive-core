
<div id='vue_task_list' class='row-fluid'>
	<div class='small-12 columns'>
		<div class='row-fluid'>
			<div class='small-12 columns'>
				<list-filter>
					<select-filter :dataset='filter.assignees' dataset-key="id" dataset-value='name' v-model='filter.assignee' label="Assignee"></select-filter>
					<select-filter :dataset='filter.creators' dataset-key="id" dataset-value='name' v-model='filter.creator' label="Creator"></select-filter>
					<select-filter :dataset='filter.task_groups' dataset-key="id" dataset-value='name' v-model='filter.task_group' label="Task Group"></select-filter>
				</list-filter>
			</div>
		</div>
		<div class='row-fluid'>
			<div class='small-12 columns'>
				<html-table :header="['ID', 'Title', 'Task Group', 'Assignee', 'Type', 'Priority', 'Status', 'Due']" :data='task_list' :include="['id', 'title', 'task_group_name', 'assignee_name', 'task_type', 'priority', 'status', 'dt_due']"></html-table>
			</div>
		</div>
	</div>
</div>
<script>

	var vue_task_list = new Vue({
		el: '#vue_task_list',
		data: {
			filter: {
				assignees: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getSelectOptionTitle()];}, $w->Auth->getUsers())); ?>,
				creators: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getFullName()];}, $w->Auth->getUsers())); ?>,
				task_groups: <?php echo json_encode(array_map(function($task_group) {return ['id' => $task_group->id, 'name' => $task_group->title];}, $w->Task->getTaskGroups())); ?>,
				assignee: null,
				creator: null,
				task_group: null,
				task_type: 'task_types',
				task_priority: 'task_priority',
				task_status: 'statuses',
				closed: 'is_closed'
			},
			task_list: []
		},
		methods: {
			getTaskList: function() {
				var _this = this;
				$.ajax('/task-vue/task_list', {

				}).done(function(response) {
					var _response = JSON.parse(response);
					_this.task_list = _response.data;
				});
			}
		},
		created: function() {
			this.getTaskList();
		}
	});

</script>