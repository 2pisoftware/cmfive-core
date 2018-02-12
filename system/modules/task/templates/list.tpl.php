
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
						<tr><th v-for="(head, index) in task_table_header" v-on:click="sort(head.key)" :key="head.key">{{ head.value }} <i class='fas' :class='{"fa-angle-up": (sort_direction == 1), "fa-angle-down": (sort_direction == -1)}'></i></th></tr>
					</thead>
					<tbody>
						<tr v-for="_task in task_list">
							<td v-html="_task['id']"></td>
							<td><a :href="_task['task_url']">{{ _task['title'] }}</a></td>
							<td><a :href="_task['task_group_url']">{{ _task['task_group_title'] }}</td>
							<td v-html="_task['assignee_name']"></td>
							<td v-html="_task['task_type']"></td>
							<td v-html="_task['priority']"></td>
							<td>
								{{ _task['status'] }}
							</td>
							<td>{{ _task['dt_due'] | formatDate }}</td>
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
			sort_key: 'id',
			sort_direction: -1,
			task_list: [],
			task_table_header: [
				{key: 'id', value: 'ID', sorting: true},
				{key: 'title', value: 'Title', sorting: false},
				{key: 'task_group_title', value: 'Task Group', sorting: false},
				{key: 'assignee_name', value: 'Assignee', sorting: false},
				{key: 'task_type', value: 'Type', sorting: false},
				{key: 'priority', value: 'Priority', sorting: false},
				{key: 'status', value: 'Status', sorting: false},
				{key: 'dt_due', value: 'Due', sorting: false}
			]
		},
		methods: {
			getTaskList: function() {
				var _this = this;
				$.ajax('/task-ajax/task_list', {

				}).done(function(response) {
					var _response = JSON.parse(response);
					_this.task_list = _response.data;
				});
			},
			sort: function(key) {
				if (this.sort_key != key) {
					this.sort_direction = 1;
					this.sort_key = key;
				} else {
					this.sort_direction *= -1;
				}

				for(var i in this.task_table_header) {
					if (this.task_table_header[i].key == key) {
						this.task_table_header[i].sorting = true;
					} else {
						this.task_table_header[i].sorting = false;
					}
				}

				console.log('key', this.sort_key, 'dir', this.sort_direction);

				var _this = this;
				_this.task_list.sort(function(a, b) {
					if (key in a && key in b && a[key] !== null && b[key] !== null) {
						return (a[key].localeCompare(b[key]) * _this.sort_direction);
					} else {
						return 0;
					}
				});
			},
			isSortKey: function(key) {
				console.log(this.sort_key, key);
				return this.sort_key == key;
			}
		},
		created: function() {
			this.getTaskList();
		}
	});

</script>