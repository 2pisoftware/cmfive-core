
<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
</div>

<div id='vue_task_list' class="container-fluid" style="position: relative;">
	<!-- <div id="options">
		<a class="tiny button radius" style="background-color: #68C2CD;" href="/task/edit/?gid=filter.task_group_id">Quick View</a>
		<a href="/task/list"><button class="tiny button radius" style="background-color: #FF7A13;">View</button></a>

		<a data-reveal-ajax="true" data-reveal-id="modal_edit" href="/task/edit/task_id"><button style="background-color: #59BC3B;" class='tiny button radius'>Edit</button></a>
		<a class="tiny button radius" style="background-color: #95ACBC;" href="/task/duplicatetask/task_id">Duplicate</a>
		
		<button class="tiny button radius" style="background-color: #D12229;" data-reveal-id="delete-modal">Delete</button>
	</div> -->
	<div class="row-fluid">
		<div class="small-12 columns">
			<ul class="small-block-grid-2 large-block-grid-5 cmfive__filter">
				<li>
					<label>Assignee</label>
					<select v-model="filter.assignee_id" placeholder="select assignee">
						<option v-for="option in assignees" :value="option.value" v-html="option.text">
					</select>
				</li>

				<li>
					<label>Creator</label>
					<select v-model="filter.creator_id" placeholder="select creator">
						<option v-for="option in creators" :value="option.value" v-html="option.text">
					</select>
				</li>

				<li>
					<label>Type</label>
					<select v-model="filter.task_type" placeholder="select task type">
						<option v-for="option in task_types" :value="option.value" v-html="option.text">
					</select>
				</li>
				<li>
					<label>Priority</label>
					<select v-model="filter.priority" placeholder="select priority">
						<option v-for="option in priority_list" :value="option.value" v-html="option.text">
					</select>
				</li>

				<li>
					<label>Status</label>
					<select v-model="filter.status" placeholder="select status">
						<option v-for="option in statuslist" :value="option.value" v-html="option.text">
					</select>
				</li>

				<li>
					<label>Task Group</label>
					<select v-model="filter.task_group_id" placeholder="select task group">
						<option v-for="option in task_groups" :value="option.value" v-html="option.text">
					</select>
				</li>
			</ul>
		</div>
	</div>

	<div class="row-fluid">
		<div class="small-12 columns">
			<table style="width:100%" v-if="task_list" id="task_list">
				<thead>
					<tr>
						<th v-for="field in header" @click="sort(field.name, $event)" :id="field.name">{{field.caption}} <span><i class="fas fa-sort"></i></span></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="task in paginatedCandidates" :class="{ test: task.status === 'Overdue' || task.priority === 'Urgent' }">
						<td v-html="task.id"></td>
						<td><a data-reveal-ajax="true" data-reveal-id="task_modal" :href="task.task_url" v-html="task.title"></a></td>
						<td><a :href="task.task_group_url" v-html="task.task_group_title"></a></td>
						<td v-html="task.assignee_name"></td>
						<td v-html="task.task_type"></td>
						<td v-html="task.priority"></td>
						<td v-html="task.status"></td>
						<td v-html="task.dt_due"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class='row-fluid'>
		<div class='small-10 columns' style="min-height:1px;"></div>
		<div class='small-2 columns'>
			<label for="selectPerPage">Tasks per page:</label>
			<select v-model="pageSize" id="selectPerPage">
				<option value="10">10</option>
				<option value="20">20</option>
				<option value="30">30</option>
				<option value="40">40</option>
				<option value="50">50</option>
			</select>
		</div>
	</div>

	<!-- <pagination v-if="tableData" v-on:currentpagechanged="onCurrentPageChanged" :data_count="tableData.length" :items_per_page="pageSize"></pagination> -->
</div>

<script>
	var vue = new Vue({
		el: '#vue_task_list',
		components: {
			// "model-list-select": VueSearchSelect.ModelListSelect,
			// "pagination": TwoPiPagination
		},
		data: {
			assignees: <?php echo json_encode(array_map(function($user) {return ['value' => $user['assignee_id'], 'text' => $user['fullname']];}, $assignees)); ?>,
			creators: <?php echo json_encode(array_map(function($user) {return ['value' => $user["creator_id"], 'text' => $user['fullname']];}, $creators)); ?>,
			task_groups: <?php echo json_encode(array_map(function($task_group) {return ['value' => $task_group->id, 'text' => $task_group->title];}, $task_groups)); ?>,
			task_types: <?php echo json_encode(array_map(function($task_type) {return ['value' => strtolower($task_type['task_type']), 'text' => $task_type['task_type']];}, $task_types)); ?>,
			priority_list: <?php echo json_encode(array_map(function($priority) {return ['value' => strtolower($priority['priority']), 'text' => $priority['priority']];}, $priority_list)); ?>,
			statuslist: <?php echo json_encode(array_map(function($task_status) {return ['value' => strtolower($task_status['status']), 'text' => $task_status['status']];}, $status_list)); ?>,
			closed: 'is_closed',

			task_list: [],
			start: 0,
			end: 10,
			currentSort:'id',
			desc: false,

			filter: {
				status: null,
				task_type: null,
				priority: null,
				assignee_id: null,
				creator_id: null,
				task_group_id: null
			},
			pageSize: 10,
			chunk_size: 5,
			header: [
				{ name: "id", caption: "ID" },
				{ name: "title", caption: "Title" },
				{ name: "task_group_title", caption: "Task Group" },
				{ name: "assignee_name", caption: "Assignee" },
				{ name: "task_type", caption: "Type" },
				{ name: "priority", caption: "Priority" },
				{ name: "status", caption: "Status" },
				{ name: "dt_due", caption: "Due" }
			]
		},
		watch: {
			filter: {
				handler: function (val, oldVal) {
					// this.assignees = val === "" ? [] : [ { text: this.filter.assignee_id, value: this.filter.assignee_id } ];
				},
				deep: true
			},

			pageSize: function(val) {
				this.start = 0;
				this.end = val;
			}
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

			assignee_autocomplete: function(text) {
				var _this = this;
				if (text === "") return;
				$.ajax({
					method: "GET",
					url: "/task-ajax/assignee_autocomplete",
					data: { filter: text }
				}).done(function(response) {
					var _response = JSON.parse(response);
					_this.assignees = _response.data;
				});
			},

			sort: function(s, event) {
				document.getElementById(this.currentSort).getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort"></i>';

				if (s === this.currentSort) {
					this.desc = !this.desc;
				}

				this.currentSort = s;

				if (!this.desc) {
					event.target.getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort-down"></i>';
				} else {
					event.target.getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort-up"></i>';
				}
			},

			onCurrentPageChanged: function(params) {
				this.start = params.start;
				this.end = params.end;
			}
		},
		created: function() {
			this.getTaskList();
		},

		computed: {
			tableData: function() { return filterSort(this.desc, this.task_list, this.currentSort, this.filter); },
			paginatedCandidates: function() { return paginate(this.tableData, this.start, this.end); }
		}
	});
</script>
