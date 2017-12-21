/**
 * Generic form component
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
Vue.component('generic-form', {
	props: {
		form: {
			type: Object,
			required: true
		},
		objectId: {
			type: String,
			requried: true
		},
		objectType: {
			type: String,
			required: true
		},
		listLimit: {
			type: Number,
			required: false,
			default: -1
		},
		shouldPaginate: {
			type: Boolean,
			required: false, 
			default: false
		},
		pageSize: {
			type: Number,
			required: false,
			default: 20
		}
	},
	data: function() {
		return {
			instances: []
		}
	},
	computed: {
		getInstances: function() {
			var _this = this;
			$.ajax('/form-vue/get_form_instances/' + this.form.id + '/' + this.objectType + '/' + this.objectId).done(function(response) {
				_this.instances = response.data;
			});
		}
	},
	methods: {
		getTableHeaders: function() {

		},
		getSummaryRow: function() {

		}
	},
	watch: {
		form: function() {
			this.getInstances();
		}
	},
	template: '<div class="row-fluid"> \
		<h4 v-bind:html="form.title"></h4> \
		<button>Button to add a new instance</button> \
		<table class="small-12 columns" v-show="instances.length > 0"> \
			<thead> \
				<tr v-bind:html="getTableHeaders()"><td>Actions</td></tr> \
			</thead> \
			<tbody> \
				<tr v-for="instance in instances"> \
					<td v-bind:html="instance.table_row"></td> \
					<td> \
						<button>Button to edit an instance</button> \
						<button>Button to delete an instance</button> \
					</td> \
				</tr> \
				<tr v-bind:html="getSummaryRow()"></tr> \
			</tbody> \
		</table> \
	</div>'
});