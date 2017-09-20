
Vue.component('modal', {

	props: {
		id: {
			type: String,
			required: true
		},
		modalClass: String,
		showClose: {
			type: Boolean,
			required: false,
			default: true
		},
		modalTitle: String
	},
	template:  '<div :id="id" class="reveal-modal" :class="modalClass" data-reveal :aria-labelledby="getComputedId" aria-hidden="true" role="dialog"> \
					<h2 :id="getComputedId" v-if="modalTitle">{{ modalTitle }}</h2> \
					<slot></slot> \
					<a v-if="showClose" class="close-reveal-modal" aria-label="Close">&#215;</a> \
				</div>',
	data: function() {
		return {
			computedId: ''
		}
	},
	computed: {
		getComputedId: function() {
			if (!this.computedId) {
				this.computedId = Math.random().toString(36).substr(2, 5);
			}

			return 'vue_modal_' + this.computedId;
		}
	}

});