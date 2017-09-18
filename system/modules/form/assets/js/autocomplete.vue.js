// Thanks to http://fareez.info/blog/vuejs/create-your-own-autocomplete-using-vuejs-2/
Vue.component('autocomplete', {
	props: {
		value: {
			type: String,
			required: false
		},
		suggestions: {
			type: Array,
			required: true
		},
		id: {
			type: String,
			required: true
		}
	},
	data: function() {
		return {
			open: false,
			current: 0,
			suggestion: ''
		}
	},
	template: '<div style="position:relative"> \
		<input type="text" :value="value" v-on:input="updateValue($event.target.value)" v-on:keydown.down="down" v-on:keydown.up="up" /> \
		<ul :id="id" v-bind:class="{\'open\':openSuggestion}" data-dropdown-content aria-hidden="true" tabindex="-1" style="width:100%"> \
			<li v-for="(suggestion, index) in matches" v-bind:class="{\'active\': isActive(index)}" v-on:click="suggestionClick(index)"> \
				<a href="#">{{ suggestion.name }}</a> \
			</li> \
		</ul> \
	</div>',
	computed: {
		// Filtering the suggestion based on the input
		matches: function() {
			return this.suggestions.filter(function(obj) {
				console.log(obj.name, this.value);
				return obj.name.indexOf(this.value) >= 0;
			});
		},
		openSuggestion: function() {
			var result = this.selection !== '' && this.matches.length !== 0 && this.open === true;
			if (result) {
				$("#" + this.id).show();
				// Foundation.libs.dropdown.open($('#' + this.id));
				// $('#' + this.id).foundation('dropdown', 'open');
			} else {
				$("#" + this.id).hide();
				// Foundation.libs.dropdown.close($('#' + this.id));
				// $('#' + this.id).foundation('dropdown', 'close');
			}
			return result;
		}
	},
	methods: {
		// Triggered the input event to cascade the updates to 
		// parent component
		updateValue: function(value) {
			if (this.open === false) {
				this.open = true;
				this.current = 0;
			}
			this.$emit('input', value);
		},
		// When enter key pressed on the input
		enter: function() {
			this.$emit('input', this.matches[this.current].name);
			this.open = false;
		},
		// When up arrow pressed while suggestions are open
		up: function() {
			if (this.current > 0) {
				this.current--;
			}
		},
		// When down arrow pressed while suggestions are open
		down: function() {
			if (this.current < this.matches.length - 1) {
				this.current++;
			}
		},
		// For highlighting element
		isActive: function(index) {
			return index === this.current
		},
		// When one of the suggestion is clicked
		suggestionClick: function(index) {
			this.$emit('input', this.matches[index].name);
			this.open = false;
		}
	}
});