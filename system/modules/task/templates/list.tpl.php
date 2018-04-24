<script src='/system/templates/vue-components/form/elements/vue-tables-2.min.js'></script>
<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>

<style>
    .VueTables__filters-row {
        display: none;
    }
    
    .VuePagination__pagination {
        display: table !important; 
        margin-left: auto !important; 
        margin-right: auto !important;
    }
</style>

<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"> 
</div>

<div id='vue_task_list' class="container-fluid">
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <div style="">
            <label>Assignee</label>
            <model-list-select style="" ref="assigneeselect" v-on:searchchange="assignee_autocomplete" v-model="assignee_id" :list="filter.assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="">
            <label>Creator</label>
            <model-list-select style="" v-model="creator_id" :list="filter.creators" placeholder="select creator" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="">
            <label>Group</label>
            <model-list-select style="" v-model="task_group_id" :list="filter.task_groups" placeholder="select task group" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="">
            <label>Status</label>
            <model-list-select style="" v-model="task_status" :list="filter.task_statuslist" placeholder="select status" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="">
            <label>Priority</label>
            <model-list-select style="" v-model="task_priority" :list="filter.priority_list" placeholder="select priority" option-value="value" option-text="text"></model-list-select>
            </div>
            
            <div style="">
            <label>Type</label>
            <model-list-select style="" v-model="task_type" :list="filter.task_types" placeholder="select task type" option-value="value" option-text="text"></model-list-select>
            </div>
        </div>
    </div>
    
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <v-client-table :columns="columns" :data="task_list" :options="options" ref="clientTable" id="clientTable" name="clientTable">
                <!--<a slot="uri" slot-scope="props" target="_blank" :href="props.row.uri" class="glyphicon glyphicon-eye-open"></a>
                <div slot="child_row" slot-scope="props">
                    The link to {{props.row.name}} is <a :href="props.row.uri">{{props.row.uri}}</a>
                </div>-->
            </v-client-table>
        </div>
    </div>
</div>

<script>
    var params = null;
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
				assignees: [],
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
                            headings: {
                                id: 'ID',
                                dt_due: 'Date due',
                                task_group_title: 'Task group',
                                assignee_name: 'Assignee'
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
                watch: {
                    task_status: function(val) {
                        this.$refs.clientTable.setFilter({ status: val });
                    },
                    
                    task_type: function(val) {
                        this.$refs.clientTable.setFilter({ task_type: val });
                    },
                    
                    task_priority: function(val) {
                        this.$refs.clientTable.setFilter({ priority: val });
                    },
                    
                    assignee_id: function(val) {
                        this.$refs.clientTable.setFilter({ assignee_name: val });
                        this.filter.assignees = val === "" ? [] : [ { text: this.assignee_id, value: this.assignee_id } ];
                    },
                    
                    creator_id: function(val) {
                        this.$refs.clientTable.setFilter({ priority: val });
                    },
                    
                    task_group_id: function(val) {
                        this.$refs.clientTable.setFilter({ task_group_title: val });
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
                            _this.filter.assignees = _response.data;
                        });
                    }
		},
		created: function() {
                    this.getTaskList();
		}
	});
</script>