<div id='vue_task_list' class="container-fluid">
    <div class='row-fluid'>
        <div class='small-3 columns'>
            <label>Assignee</label>
            <autocomplete :list="filter.assignees" v-on:autocomplete-select="setAssignee" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
        <div class='small-3 columns'>
            <label>Creator</label>
            <autocomplete :list="filter.creators" v-on:autocomplete-select="setCreator" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
        <div class='small-3 columns'>
            <label>Task Group</label>
            <autocomplete :list="filter.task_groups" v-on:autocomplete-select="setTaskgroup" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
        <div class='small-3 columns'>
            <label>Task status</label>
            <autocomplete :list="filter.task_statuslist" v-on:autocomplete-select="setTaskstatus" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
    </div>
    <div class='row-fluid'>
        <div class='small-3 columns'>
            <label>Priority</label>
            <autocomplete :list="filter.priority_list" v-on:autocomplete-select="setPriority" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
        <div class='small-3 columns'>
            <label>Task type</label>
            <autocomplete :list="filter.task_types" v-on:autocomplete-select="setTasktype" property="name" :required="false" :threshold="0"></autocomplete>
        </div>
        <div class='small-3 columns'>

        </div>
        <div class='small-3 columns'>

        </div>
    </div>
    <div class='row-fluid'>
        <div class='small-12 columns text-center'>
            <button v-on:click="getTaskList()" class="tiny button info radius" style="width: 45%;">Filter</button>
            <button v-on:click="getTaskList(true)" class="tiny button info radius" style="width: 45%;">Reset</button>
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

<script> 
	var vue_task_list = new Vue({
		el: '#vue_task_list',
		data: {
			filter: {
				assignees: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getSelectOptionTitle()];}, $w->Auth->getUsers())); ?>,
				creators: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getFullName()];}, $w->Auth->getUsers())); ?>,
				task_groups: <?php echo json_encode(array_map(function($task_group) {return ['id' => $task_group->id, 'name' => $task_group->title];}, $w->Task->getTaskGroups())); ?>,
				task_types: <?php echo json_encode(array_map(function($task_type) {return ['id' => $task_type['task_type'], 'name' => $task_type['task_type']];}, $w->db->get("task")->select()->select("DISTINCT task_type")->fetchAll())); ?>,
                                priority_list: <?php echo json_encode(array_map(function($priority) {return ['id' => $priority['priority'], 'name' => $priority['priority']];}, $w->db->get("task")->select()->select("DISTINCT priority")->fetchAll())); ?>,
                                task_statuslist: <?php echo json_encode(array_map(function($task_status) {return ['id' => $task_status['status'], 'name' => $task_status['status']];}, $w->db->get("task")->select()->select("DISTINCT status")->fetchAll())); ?>,
				assignee_id: null,
				creator_id: null,
				task_group_id: null,
				task_type: null,
				task_priority: null,
				task_status: null,
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
			getTaskList: function(reset = false) {
				var _this = this;
                                var params = null;
                                
                                if (reset) {
                                    this.filter.assignee_id = null;
                                    this.filter.creator_id = null;
                                    this.filter.task_group_id = null;
                                    this.filter.task_type = null;
                                    this.filter.task_priority = null;
                                    this.filter.task_status = null;
                                    
                                    var acs = document.getElementsByClassName("autocomplete");
                                    for (var i = 0; i < acs.length; i++) {
                                        acs[i].lastElementChild.value = null;
                                    }
                                }
                                
                                else if (!reset) {
                                    params = {
                                        assignee_id: this.filter.assignee_id,
                                        creator_id: this.filter.creator_id,
                                        task_group_id: this.filter.task_group_id,
                                        task_type: this.filter.task_type,
                                        priority: this.filter.task_priority,
                                        status: this.filter.task_status
                                    };
                                
                                    for (var v in params) { 
                                        if (params[v] === null || params[v] === undefined || params[v] === "") {
                                            delete params[v];
                                        }
                                    }
                                }
                                
				$.ajax({url: '/task-ajax/task_list', data: params }, {

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
			},
                        setPriority: function(selectedValue) {
                            this.filter.task_priority = selectedValue;
			},
                        setAssignee: function(selectedValue) {
                            this.filter.assignee_id = selectedValue;
                        },
                        setCreator: function(selectedValue) {
                            this.filter.creator_id = selectedValue;
                        },
                        setTaskgroup: function(selectedValue) {
                            this.filter.task_group_id = selectedValue;
                        },
                        setTasktype: function(selectedValue) {
                            this.filter.task_type = selectedValue;
                        },
                        setTaskstatus: function(selectedValue) {
                            this.filter.task_status = selectedValue;
                        }
		},
		created: function() {
			this.getTaskList();
		}
	});

</script>