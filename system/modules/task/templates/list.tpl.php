<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/vue-components/html/twopipagination.js'></script>
<script src='/system/templates/js/natsort.min.js'></script>
<script src='/system/templates/vue-components/html/filterSort.js'></script>

<style>

    #task_list > thead > tr > th {
        cursor: pointer;
    }

     #options {
        display: none;
        background-color: Silver;
        text-align: right;
        position: absolute;
        z-index: 10000;
        opacity: 0.5;
    }
</style>

<h1>Task List</h1>

<div id="task_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
</div>

<div id='vue_task_list' class="container-fluid" style="position: relative;">
    <div id="options">
        <a class="tiny button radius" style="background-color: #68C2CD;" href="/task/edit/?gid=filter.task_group_id">Quick View</a>
        <a href="/task/list"><button class="tiny button radius" style="background-color: #FF7A13;">View</button></a>

        <a data-reveal-ajax="true" data-reveal-id="modal_edit" href="/task/edit/task_id"><button style="background-color: #59BC3B;" class='tiny button radius'>Edit</button></a>
        <a class="tiny button radius" style="background-color: #95ACBC;" href="/task/duplicatetask/task_id">Duplicate</a>
        <!-- if can delete -->
        <button class="tiny button radius" style="background-color: #D12229;" data-reveal-id="delete-modal">Delete</button>
    </div>
    <div class='row-fluid'>
        <div class='medium-12 large-4 columns'>
            <label>Assignee</label>
            <model-list-select style="" v-model="filter.assignee_id" :list="assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>
            <!--<model-list-select style="" ref="assigneeselect" v-on:searchchange="assignee_autocomplete" v-model="filter.assignee_id" :list="assignees" placeholder="select assignee" option-value="value" option-text="text"></model-list-select>-->
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Creator</label>
            <model-list-select style="" v-model="filter.creator_id" :list="creators" placeholder="select creator" option-value="value" option-text="text"></model-list-select>
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Type</label>
            <model-list-select style="" v-model="filter.task_type" :list="task_types" placeholder="select task type" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>
    <div class='row-fluid'>
        <div class='medium-12 large-4 columns'>
            <label>Priority</label>
            <model-list-select style="" v-model="filter.priority" :list="priority_list" placeholder="select priority" option-value="value" option-text="text"></model-list-select>
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Status</label>
            <model-list-select style="" v-model="filter.status" :list="statuslist" placeholder="select status" option-value="value" option-text="text"></model-list-select>
        </div>

        <div class='medium-12 large-4 columns'>
            <label>Task Group</label>
            <model-list-select style="" v-model="filter.task_group_id" :list="task_groups" placeholder="select task group" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>

    <div class='row-fluid columns' style="height: 2em;"></div>

    <table style="width:100%" v-if="task_list" id="task_list">
        <thead>
            <tr>
                <th v-for="field in header" @click="sort(field.name, $event)" :id="field.name">{{field.caption}} <span><i class="fas fa-sort"></i></span></th>
            </tr>
        </thead>
        <tbody>
            <tr @mouseenter="show_options($event)" @mouseleave="hide_options" v-for="task in paginatedCandidates" :class="{ test: task.status === 'Overdue' || task.priority === 'Urgent' }">
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

    <div class='row-fluid columns' style="height: 2em;"></div>

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

    <pagination v-if="tableData" v-on:currentpagechanged="onCurrentPageChanged" :data_count="tableData.length" :items_per_page="pageSize"></pagination>
</div>

<script>
	var vue = new Vue({
		el: '#vue_task_list',
        components: {
            "model-list-select": VueSearchSelect.ModelListSelect,
            "pagination": TwoPiPagination
        },
		data: {
            assignees: <?php echo $assignees; ?>,
            creators: <?php echo $creators; ?>,
            task_groups: <?php echo $task_groups; ?>,
            task_types: <?php echo $task_types; ?>,
            priority_list: <?php echo $priority_list; ?>,
            statuslist: <?php echo $statuslist; ?>,
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
                    },

                    show_options: function(event) {
                        /* var coord = event.target.getBoundingClientRect();

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

                        $divOverlay.mouseleave(function() {
                            $divOverlay.hide();
                        }); */
                    },

                    hide_options: function() {

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
