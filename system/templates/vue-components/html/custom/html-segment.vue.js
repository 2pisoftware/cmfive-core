/**
 * 
 */

Vue.component('html-segment', {
	props: {
		title: {
			type: String,
			required: true
		}
	},
	template: '<div class="html-segment"><div class="segment-header" v-html="title"></div><div class="segment-content"><slot></slot></div></div>'
});