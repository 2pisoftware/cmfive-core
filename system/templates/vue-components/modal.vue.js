
Vue.component('modal', {

	props: {
		id: {
			type: String,
			required: true
		},
		'modal-class': String,
		'show-close': {
			type: Boolean,
			required: false,
			default: true
		},
		'modal-title': String
	},
	template: '<div id="id" class="reveal-modal" v-bind:class="modalClass" data-reveal v-bind:aria-labelledby="getComputedId()" aria-hidden="true" role="dialog"><h2 v-bind:id="getComputedId()" v-if="modalTitle">{{ modalTitle }}</h2><slot></slot></div>',
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