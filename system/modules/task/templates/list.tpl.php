<script src='/system/templates/vue-components/form/elements/vue-tables-2.min.js'></script>
<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>

<style>
    .VueTables__filters-row {
        display: none;
    }
</style>

<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"> 
</div>

<div id='vue_task_list' class="container-fluid">
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <div style="display: inline-block;">
            <label>Assignee</label>
            <model-list-select style="width: 20em;" v-model="assignee_id" :list="filter.assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="display: inline-block;">
            <label>Creator</label>
            <model-list-select style="width: 15em;" v-model="creator_id" :list="filter.creators" placeholder="select creator" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="display: inline-block;">
            <label>Group</label>
            <model-list-select style="width: 20em;" v-model="task_group_id" :list="filter.task_groups" placeholder="select task group" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="display: inline-block;">
            <label>Status</label>
            <model-list-select style="width: 15em;" v-model="task_status" :list="filter.task_statuslist" placeholder="select status" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="display: inline-block;">
            <label>Priority</label>
            <model-list-select style="width: 15em;" v-model="task_priority" :list="filter.priority_list" placeholder="select priority" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="display: inline-block;">
            <label>Type</label>
            <model-list-select style="width: 15em;" v-model="task_type" :list="filter.task_types" placeholder="select task type" option-value="value" option-text="text"></model-list-select>
            </div>
        </div>
    </div>
    
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <!--<v-server-table url="" :columns="columns" :options="options" ref="serverTable"></v-server-table>-->
            <v-client-table :columns="columns" :data="task_list" :options="options" ref="clientTable" id="clientTable" name="clientTable">
                <!--<a slot="uri" slot-scope="props" target="_blank" :href="props.row.uri" class="glyphicon glyphicon-eye-open"></a>
                <div slot="child_row" slot-scope="props">
                    The link to {{props.row.name}} is <a :href="props.row.uri">{{props.row.uri}}</a>
                </div>-->
            </v-client-table>
            
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
    
    //Vue.use(VueTables.ServerTable);
    Vue.use(VueTables.ClientTable);
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
	var test = new Vue({ 
		el: '#vue_task_list',
		data: {
                    task_status: "",
                    task_type: "",
                    task_priority: "",
                    assignee_id: "",
                    creator_id: "",
                    task_group_id: "",
			filter: {
				assignees: <?php echo $assignees; ?>,
				creators: <?php echo $creators; ?>,
				task_groups: <?php echo $task_groups; ?>,
				task_types: <?php echo $task_types; ?>,
                                priority_list: <?php echo $priority_list; ?>,
                                task_statuslist: <?php echo $task_statuslist; ?>,
				closed: 'is_closed'
			},
                        options: {
                            pagination: { dropdown: false, edge: true },
                            filterByColumn: true,
                            filterable: true,
                            perPage: 2,
                            customFilters: [{
                                name: 'status',
                                callback: function (row, query) {
                                    return row.name[0] === query;
                                }
                                }
                            ],
                            /*listColumns: {
                                priority: <?php echo $priority_list_select; ?>,
                                status: <?php echo $task_statuslist_select; ?>
                            },*/
                            //dateColumns: {
                                
                            //},
                            headings: {
                                id: 'ID',
                                dt_due: 'Date due',
                                task_group_title: 'Task group',
                                assignee_name: 'Assignee'
                            },
                            /*responseAdapter: function(resp) {
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
                            },   */
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
                watch: {
                    task_status: function(val) {
                        this.$refs.clientTable.setFilter({ status: val });
                        //this.$emit('vue-tables.clientTable.filter::status', val);
                    },
                    
                    task_type: function(val) {
                        this.$refs.clientTable.setFilter({ task_type: val });
                    },
                    
                    task_priority: function(val) {
                        this.$refs.clientTable.setFilter({ priority: val });
                    },
                    
                    assignee_id: function(val) {
                        this.$refs.clientTable.setFilter({ assignee_name: val });
                    },
                    
                    creator_id: function(val) {
                        this.$refs.clientTable.setFilter({ priority: val });
                    },
                    
                    task_group_id: function(val) {
                        this.$refs.clientTable.setFilter({ task_group_title: val });
                    }
                },
		methods: {
			getTaskList: function(reset = false) {
                            var _this = this;
                            $.ajax('/task-ajax/task_list', {

                            }).done(function(response) {
                                var _response = JSON.parse(response);
                                _this.task_list = _response.data;
                            });
                                /*if (reset) {
                                    this.assignee_id = null;
                                    this.creator_id = null;
                                    this.task_group_id = null;
                                    this.task_type = null;
                                    this.task_priority = null;
                                    this.task_status = null;
                                    
                                    params = null;
                                    
                                    this.$refs.serverTable.getData();
                                    this.$refs.serverTable.refresh();
                                }
                                
                                else if (!reset) {
                                     params = {
                                        assignee_id: this.assignee_id,
                                        creator_id: this.creator_id,
                                        task_group_id: this.task_group_id,
                                        task_type: this.task_type,
                                        priority: this.task_priority,
                                        status: this.task_status
                                    };
                                
                                    for (var v in params) { 
                                        if (params[v] === null || params[v] === undefined || params[v] === "") {
                                            delete params[v];
                                        }
                                    }
                                    
                                    this.$refs.serverTable.getData();
                                    this.$refs.serverTable.refresh();
                                }*/
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
                    this.getTaskList();
		}
	});
</script>