/**
 * 
 */

Vue.component('select-filter', {
	model: {
		prop: 'selectedValue',
		event: 'change'
	},
	props: {
		dataset: {
			type: Array,
			required: true
		},
		datasetKey: {
			type: String,
			required: true
		},
		datasetValue: {
			type: String,
			required: true
		},
		label: String,
		selectId: {},
		selectClass: {},
		selectedValue: {}
	},
	methods: {
		emitChangeEvent: function(value) {
			this.$emit('change', value);
		}
	},
	template:  '<li> \
					<label>{{ label }} \
						<select v-bind:id="selectId" class="vue_select_filter" v-bind:class="selectClass" ref="select" v-bind:value="selectedValue" v-on:change="emitChangeEvent($event.target.value)"> \
							<option v-for="data in dataset" v-bind:value="data[datasetKey]">{{ data[datasetValue] }}</option>\
						</select> \
					</label> \
				</li>'
});
