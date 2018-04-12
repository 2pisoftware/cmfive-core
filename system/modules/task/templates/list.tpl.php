<script src='/system/templates/vue-components/form/elements/vue-tables-2.min.js'></script>
<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>

<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"> 
</div>

<div id='vue_task_list' class="container-fluid">
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <div style="display: inline-block;">
            <label>Assignee</label>
            <model-list-select v-model="filter.assignee_id" :list="filter.assignees" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
            <div style="display: inline-block;">
            <label>Creator</label>
            <model-list-select v-model="filter.creator_id" :list="filter.creators" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            </div>
            <label>Group</label>
            <model-list-select v-model="filter.task_group_id" :list="filter.task_groups" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            <label>Status</label>
            <model-list-select v-model="filter.task_status" :list="filter.task_statuslist" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            <label>Priority</label>
            <model-list-select v-model="filter.task_priority" :list="filter.priority_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
            
            <label>Type</label>
            <model-list-select v-model="filter.task_type" :list="filter.task_types" placeholder="select item" option-value="value" option-text="text"></model-list-select>
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
            <v-server-table url="" :columns="columns" :options="options" ref="serverTable"></v-server-table>
            <!--<v-client-table :columns="columns" :data="task_list" :options="options">
                <a slot="uri" slot-scope="props" target="_blank" :href="props.row.uri" class="glyphicon glyphicon-eye-open"></a>
                <div slot="child_row" slot-scope="props">
                    The link to {{props.row.name}} is <a :href="props.row.uri">{{props.row.uri}}</a>
                </div>
            </v-client-table>-->
            
            <!--<v2-table :data="task_list" style="width: 100%;">
                <v2-table-column label="Name" prop="assignee_name" :sortable="true" :width="100"></v2-table-column>
                <v2-table-column label="Date" prop="date"></v2-table-column>
                <v2-table-column label="Address" prop="address"></v2-table-column>  
            </v2-table>-->  
        </div>
    </div>
    
    <!-- <div class='row-fluid'>
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
            <html-table :header="" :data='task_list' :include="['id', 'title', 'task_group_name', 'assignee_name', 'task_type', 'priority', 'status', 'dt_due']">

            </html-table> 
        </div>
    </div> -->
	
</div>

<script>
    var params = null;
    
    Vue.use(VueTables.ServerTable);
    Vue.component("model-list-select", VueSearchSelect.ModelListSelect);
    
    Vue.component('task-url', {
        props: ['data', 'index'],
        template: `<a :href="'/task/view/' + this.data.id" data-reveal-ajax="true" data-reveal-id="task_modal">{{this.data.title}}</a>`,
        methods: {
            
        }
    });
    Vue.component('task-group-url', {
        props: ['data', 'index'],
        template: `<a href="#">{{this.data.task_group_title}}</a>`
    });
	new Vue({
		el: '#vue_task_list',
		data: {
			filter: {
				assignees: <?php echo $assignees; ?>,
				creators: <?php echo $creators; ?>,
				task_groups: <?php echo $task_groups; ?>,
				task_types: <?php echo $task_types; ?>,
                                priority_list: <?php echo $priority_list; ?>,
                                task_statuslist: <?php echo $task_statuslist; ?>,
				assignee_id: "",
				creator_id: "",
				task_group_id: "",
				task_type: "",
				task_priority: "",
				task_status: "",
				closed: 'is_closed'
			},
                        options: {
                            pagination: { dropdown: false, edge: true },
                            filterByColumn: true,
                            filterable: true,
                            perPage: 2,
                            listColumns: {
                                priority: <?php echo $priority_list_select; ?>,
                                status: <?php echo $task_statuslist_select; ?>
                            },
                            //dateColumns: {
                                
                            //},
                            headings: {
                                id: 'ID',
                                dt_due: 'Date due',
                                task_group_title: 'Task group',
                                assignee_name: 'Assignee'
                            },
                            responseAdapter: function(resp) {
                                var f = resp.data;
                                return { data: f.data, count: f.count };
                            },
                            requestAdapter(data) {
                                return {
                                    orderBy: data.orderBy ? " order by " + data.orderBy + " " + (data.ascending ? "asc" : "desc") + " " : "",
                                    byColumn: data.byColumn ? data.byColumn : "",
                                    limit: data.limit,
                                    page: data.page,
                                    query: data.query,
                                    params: params ? params : ""
                                };
                            },
                            requestFunction: function (data) {
                                return $.ajax({
                                    type: "GET",
                                    url: '/task-ajax/task_list',
                                    data: data,
                                    dataType: 'json'
                                });
                            },   
                            templates: {
                                title: 'task-url',
                                task_group_title: 'task-group-url'
                            }
                        },
    
                        columns: ['id', 'title', 'task_group_title', 'assignee_name', 'task_type', 'priority', 'status', 'dt_due'],
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
                                if (reset) {
                                    this.filter.assignee_id = null;
                                    this.filter.creator_id = null;
                                    this.filter.task_group_id = null;
                                    this.filter.task_type = null;
                                    this.filter.task_priority = null;
                                    this.filter.task_status = null;
                                    
                                    params = null;
                                    
                                    this.$refs.serverTable.getData();
                                    this.$refs.serverTable.refresh();
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
                                    
                                    this.$refs.serverTable.getData();
                                    this.$refs.serverTable.refresh();
                                }
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
				return this.sort_key == key;
			}
		},
		created: function() {
                    
		}
	});
</script>