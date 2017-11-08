/**
 * 
 */
Vue.component('metadata-subform', {
	props: {
		defaultValue: {
			type: Array,
			required: false
		},
		forms: {
			type: Array,
			required: true
		}
	},
	methods: {
		isSelected: function(current_value) {
			if (this.defaultValue !== undefined) {
				for(var i in this.defaultValue) {
					if (this.defaultValue[i].meta_key == "associated_form") {
						return current_value == this.defaultValue[i].meta_value;
					}
				}
			}

			return false;
		}
	},
	template: '<div class="row"> \
		<div class="small-12 columns"> \
			<label>Associated Form \
				<select name="associated_form"> \
					<option v-for="form in forms" :value="form.id" v-html="form.title" :selected="isSelected(form.id)"></option> \
				</select> \
			</label> \
		</div> \
	</div>'
});
