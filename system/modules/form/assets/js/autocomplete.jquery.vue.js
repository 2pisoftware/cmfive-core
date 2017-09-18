
Vue.component('autocomplete', {
	props: {
		source: {
			type: Array,
			required: true
		},
		id: {
			type: String,
			required: true
		},
		prefix: {
			type: String,
			required: false,
			default: 'acp_'
		},
		value: {
			type: String,
			required: false
		}
	},
	data: function() {
		return {
			autocomplete: null
		}
	},
	template: '<div><input type="text" style="display: none;" :id="id" :name="id" :value="value" /> \
		<div class="acp_container"> \
			<input type="text" :id="acpId()" :name="acpId()" :value.once="displayValue()" v-on:keyup="checkKey($event)"/> \
		</div></div>',
	methods: {
		displayValue: function() {
			for(var i in this.source) {
				if (this.source[i].id === this.value) {
					return this.source[i].name;
				}
			}
		},
		acpId: function() {
			return this.prefix + this.id;
		},
		init: function() {
			var _this = this;
			this.autocomplete = $("#" + this.acpId()).autocomplete({
				minLength: 3, 
				source: _this.source,
				select: function(event,ui) {
					event.preventDefault();
					$("#" + _this.id).val(ui.item.value);
					$("#" + _this.acpId()).val(ui.item.label);
					// selectAutocompleteCallback(event, ui);
				},
				search: function(event, ui) {
					debugger;
				}
			});
		},
		checkKey: function(event) {
			if (event.which != 13) { 	
				$("#" + this.id).val("");
			}
		}
	},
	created: function() {
		this.init();
	}
});
