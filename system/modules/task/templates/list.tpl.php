<script src='/system/templates/vue-components/vue-tables-2.min.js'></script>
<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/js/natsort.min.js'></script>

<style>
    .test > td {
        color: #CC3647;
    }

    .test > td > a {
        color: #CC3647;
    }

    .test:nth-child(odd) {
        background-color: #F9E1E1;
    }

    .test:nth-child(even) {
        background-color: #FDF4F5;
    }

    #pi-table > thead > tr > th {
        cursor: pointer;
    }

     #options {
        display: none;
        background-color: Silver;
        text-align: center;
        position: absolute;
        z-index: 10000;
        opacity: 0.5;
    }
</style>

<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"> 
</div>

<div id='vue_task_list' class="container-fluid" style="position: relative;">
    <div id="options">serfgnvhsokgcs</div>
    <div class='row-fluid'>
        <div class='medium-12 large-4 columns'>
            <label>Assignee</label>
            <model-list-select style="" ref="assigneeselect" v-model="assignee_id" :list="filter.assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>
            <!--<model-list-select style="" ref="assigneeselect" v-on:searchchange="assignee_autocomplete" v-model="assignee_id" :list="filter.assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>-->
        </div>
            
        <div class='medium-12 large-4 columns'>
            <label>Creator</label>
            <model-list-select style="" v-model="creator_id" :list="filter.creators" placeholder="select creator" option-value="value" option-text="text"></model-list-select>
        </div>
        
        <div class='medium-12 large-4 columns'>
            <label>Task type</label>
            <model-list-select style="" v-model="task_type" :list="filter.task_types" placeholder="select task type" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>
    <div class='row-fluid'>
        <div class='medium-12 large-4 columns'>
            <label>Task priority</label>
            <model-list-select style="" v-model="task_priority" :list="filter.priority_list" placeholder="select priority" option-value="value" option-text="text"></model-list-select>
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Task status</label>
            <model-list-select style="" v-model="task_status" :list="filter.task_statuslist" placeholder="select status" option-value="value" option-text="text"></model-list-select>
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Task group</label>
            <model-list-select style="" v-model="task_group_id" :list="filter.task_groups" placeholder="select task group" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>

    <div class='row-fluid columns' style="height: 2em;"></div>

    <div class='row-fluid'>
        <div class='small-4 columns' style="min-height:1px;"></div>
        <div class='small-4 columns'>
            <label for="selectPerPage">Tasks per page:</label>
            <select v-model="pageSize" id="selectPerPage">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </select>
        </div>
        <div class='small-4 columns' style="min-height:1px;"></div>
    </div>

    <div class='row-fluid columns' style="height: 2em;"></div>

    <table style="width:100%" v-if="task_list" id="pi-table">
        <thead>
            <tr>
                <th @click="sortTable('id', $event)" id="id">ID <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('title', $event)" id="title">Title <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('task_group_title', $event)" id="task_group_title">Task group <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('assignee_name', $event)" id="assignee_name">Assigned to <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('task_type', $event)" id="task_type">Type <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('priority', $event)" id="priority">Priority <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('status', $event)" id="status">Status <span><i class="fas fa-sort"></i></span></th>
                <th @click="sortTable('dt_due', $event)" id="dt_due">Due <span><i class="fas fa-sort"></i></span></th>
            </tr>
        </thead>
        <tbody>
            <tr @mouseenter="show_options($event)" @mouseleave="hide_options" v-for="task in paginedCandidates" :class="{ test: task.status === 'Overdue' || task.priority === 'Urgent' }">
                <td>
                    {{task.id}}
                </td>
                <td>
                    <a data-reveal-ajax="true" data-reveal-id="task_modal" :href="task.task_url">{{task.title}}</a>
                </td>
                <td>
                    <a :href="task.task_group_url">{{task.task_group_title}}</a>
                </td>
                <td>
                    {{task.assignee_name}}
                </td>
                <td>
                    {{task.task_type}}
                </td>
                <td>
                    {{task.priority}}
                </td>
                <td>
                    {{task.status}}
                </td>
                <td>
                    {{task.dt_due}}
                </td>
            </tr>
        </tbody>
    </table>

    <div class='row-fluid text-center' v-if="numberOfPages > 1">
        <br>
        <button class="button tiny radius" @click="firstPage">First page</button>
        <button class="button tiny radius" @click="prev_chunk"><i class="fas fa-angle-double-left"></i></button>
        <button class="button tiny radius" @click="prevPage"><i class="fas fa-angle-left"></i></button>

        <button :class="{button: true, tiny: true, radius: true, success: currentPage === n}" v-for="n in pages" @click="if (currentPage !== n) currentPage = n">{{n}}</button>

        <button class="button tiny radius" @click="nextPage"><i class="fas fa-angle-right"></i></button>
        <button class="button tiny radius" @click="next_chunk"><i class="fas fa-angle-double-right"></i></button>
        <button class="button tiny radius" @click="lastPage">Last page</button>
        <br>
        page {{currentPage}} of {{numberOfPages}}
    </div>
    
    </div>
</div>

<script>
    var params = null;
    Vue.component("model-list-select", VueSearchSelect.ModelListSelect);
    
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
                       
			task_list: [],
		
            currentSort:'id',
            currentSortDir:'asc',
            filter_array: {
                status: null,
                task_type: null,
                priority: null,
                assignee_id: null,
                creator_id: null,
                task_group_id: null
            },
            pageSize: 2,
            currentPage: 1,
            chunk_size: 5,
            first_page: 1,
            last_page: 5,
            pages: [1, 2, 3, 4, 5]
		},
                watch: {
                    task_status: function(val) {
                        this.filter_array.status = val;
                    },
                    
                    task_type: function(val) {
                        this.filter_array.task_type = val;
                    },
                    
                    task_priority: function(val) {
                        this.filter_array.priority = val;
                    },
                    
                    assignee_id: function(val) {
                        this.filter_array.assignee_id = val;
                        //this.filter.assignees = val === "" ? [] : [ { text: this.assignee_id, value: this.assignee_id } ];
                    },
                    
                    creator_id: function(val) {
                        this.filter_array.creator_id = val;
                    },
                    
                    task_group_id: function(val) {
                        this.filter_array.task_group_id = val;
                    },

                    filter_array: {
                        handler: function (val, oldVal) {  },
                        deep: true
                    }
                },
		methods: {
			getTaskList: function() {
                /*Vue.http.get('/task-ajax/delete', {params:{id: this.id}}).then(function (response) {
                    if (response.body.data === "deleted")
                        window.location.replace("/task/list");
                },
                function (error) {

                });*/

                var _this = this;
                $.ajax('/task-ajax/task_list', {

                }).done(function(response) {
                    var _response = JSON.parse(response);
                    _this.task_list = _response.data;
                    _this.tableData = _response.data;
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
                    },

                    sortTable: function(s, event) {
                        //if s == current sort, reverse
                        document.getElementById(this.currentSort).getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort"></i>';
                        
                        if (s === this.currentSort) {
                            this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                        }
                        
                        this.currentSort = s;
                        
                        if (this.currentSortDir === 'desc') {
                            event.target.getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort-down"></i>';
                        } else {
                            event.target.getElementsByTagName("span")[0].innerHTML = '<i class="fas fa-sort-up"></i>';
                        }
                    },

                    paginate() {
                        var t = this;
                        return this.tableData.filter(function(row, index) {
                            var start = (t.currentPage-1) * t.pageSize;
                            var end = t.currentPage * t.pageSize;  

                            if (index >= start && index < end) 
                                return true;
                        });
                    },

                    nextPage: function() {
                        if ((this.currentPage * this.pageSize) < this.tableData.length) this.currentPage++;
                    },
                    prevPage: function() {
                        if (this.currentPage > 1) this.currentPage--;
                    },
                    firstPage: function() {
                        if (this.currentPage > 1) this.currentPage = 1;
                    },
                    lastPage: function() {
                        if (this.currentPage < this.numberOfPages) this.currentPage = this.numberOfPages;
                    },

                    next_chunk: function() {
                        if (this.last_page === this.numberOfPages) return;
                        
                        this.first_page += this.chunk_size;
                        this.last_page += this.chunk_size;
                        
                        this.pages = [];
                        for (var i = this.first_page; i < this.last_page + 1; i++) this.pages.push(i);
                    },

                    prev_chunk: function() {
                        if (this.first_page === 1) return;

                        this.first_page -= this.chunk_size;
                        this.last_page -= this.chunk_size;

                        this.pages = [];
                        for (var i = this.first_page; i < this.last_page + 1; i++) this.pages.push(i);
                    },

                    show_options: function(event) {
                        var coord = event.target.getBoundingClientRect();

                        var $divOverlay = $('#options');
                       
                        var bottomWidth = $(event.target).css('width');
                        var bottomHeight = $(event.target).css('height');
                        var rowPos = $(event.target).position();
                        bottomTop = rowPos.top;
                        bottomLeft = rowPos.left;
                        $divOverlay.css({
                            position: 'absolute',
                            top: bottomTop,
                            left: bottomLeft,
                            width: coord.width,
                            height: bottomHeight
                        });
                        
                        $divOverlay.show();
                    },

                    hide_options: function() {
                        $('#options').hide();
                    }
		},
		created: function() {
                    this.getTaskList();
		},

        computed: {
            numberOfPages: function() { 
                return Math.ceil(this.tableData.length / this.pageSize);
            },
            
            tableData: function() {
                var t = this;
                
                return t.task_list.sort( function(a,b) {
                    var modifier = 1;
                    if (t.currentSortDir === 'desc') modifier = -1;
                    if (a[t.currentSort] < b[t.currentSort]) return -1 * modifier;
                    if (a[t.currentSort] > b[t.currentSort]) return 1 * modifier;
                    return 0;
                }).filter(function(row, index) {
                    if (t.filter_array) {
                        for (var key in t.filter_array) {
                        if (t.filter_array[key] && t.filter_array[key] !== undefined && 
                        t.filter_array[key] !== "" && row[key].toLowerCase() !== t.filter_array[key].toLowerCase())
                            return false;
                        }
                    }
                    
                    return true;
                });
            },

            paginedCandidates: function() {
                return this.paginate();
            }
        }
	});
</script>