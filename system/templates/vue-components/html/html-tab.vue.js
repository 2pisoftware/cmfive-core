/**
 * 
 */

Vue.component('html-tab', {
	props: {
		title: {
			type: String,
			required: true
		},
		icon: {
			type: String,
			default: ''
		},
		selected: {
			type: Boolean,
			default: false
		}
	},
	data: function() {
		return {
			isActive: false
		}
	},
	computed: {
		href: function() {
			return '#' + this.title.toLowerCase().replace(/ /g, '-');
		}
	},
	mounted: function() {
		this.isActive = this.selected;
	},
	template: '<div v-show="isActive"><slot></slot></div>'
});