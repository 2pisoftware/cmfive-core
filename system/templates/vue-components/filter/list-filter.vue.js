/**
 * 
 */

Vue.component('list-filter', {
	methods: {
		child_elements: function() {
			return this.$root.$children.length;
		}
	},
	template:  '<div class="vue-list-filter"> \
					<h3>Filter</h3> \
					<ul class="small-block-grid-2 medium-block-grid-4 large-block-grid-6"> \
						<slot></slot> \
					</ul> \
				</div>'
});