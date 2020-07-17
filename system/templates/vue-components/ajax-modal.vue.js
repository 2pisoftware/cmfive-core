/**
 * Ajax modal window - it is assumed you'll use JS to open this modal
 * 
 * @param  String id required
 * @param  String modalClass optional
 * @param  Boolean showClose optional default: false
 */
Vue.component('ajax-modal', {
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
		}
	},
	template:  '<div :id="id" class="reveal-modal" :class="modalClass" data-reveal :aria-labelledby="getComputedId" aria-hidden="true" role="dialog" ref="modal"> \
					<a v-if="showClose" class="close-reveal-modal" aria-label="Close">&#215;</a> \
				</div>',
	data: function() {
		return {
			computedId: ''
		}
	},
	mounted: function() {
		var _this = this;
		$(_this.$refs.modal).on('open.fndtn.reveal', '[data-reveal]', function() {
			_this.emit('modalopen');
		});
		$(_this.$refs.modal).on('opened.fndtn.reveal', '[data-reveal]', function() {
			_this.emit('modalopened');
		});
		$(_this.$refs.modal).on('close.fndtn.reveal', '[data-reveal]', function() {
			_this.emit('modalclose');
		});
		$(_this.$refs.modal).on('closed.fndtn.reveal', '[data-reveal]', function() {
			_this.emit('modalclosed');
		});
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