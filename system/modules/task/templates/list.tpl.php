
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
				<table class="cmfive-html-table" border="0">
					<thead>
						<tr><th v-for="head in ['ID', 'Title', 'Task Group', 'Assignee', 'Type', 'Priority', 'Status', 'Due']">{{ head }}</th></tr>
					</thead>
					<tbody>
						<tr v-for="_task in task_list">
							<td v-html="_task['id']"></td>
							<td v-html="_task['title']"></td>
							<td v-html="_task['task_group_name']"></td>
							<td v-html="_task['assignee_name']"></td>
							<td v-html="_task['task_type']"></td>
							<td v-html="_task['priority']"></td>
							<td>
								
							</td>
							<td v-html="_task['dt_due']"></td>
						</tr>
					</tbody>
				</table>
				<!-- <html-table :header="" :data='task_list' :include="['id', 'title', 'task_group_name', 'assignee_name', 'task_type', 'priority', 'status', 'dt_due']">
					
				</html-table> -->
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
				task_types: <?php echo json_encode($task_types); ?>,
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
				$.ajax('/task-ajax/task_list', {

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