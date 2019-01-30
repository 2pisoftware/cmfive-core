/**
 * 
 */

Vue.component('html-table', {
	props: {
		header: Array,
		data: {
			type: Array,
			required: true
		},
		include: {
			type: Array,
			required: true
		}
	},
	template:  '<table class="cmfive-html-table" border="0"> \
					<thead v-if="header"><tr><th v-for="head in header">{{ head }}</th></tr></thead> \
					<tbody><tr v-for="_data in data"><td v-for="_include in include" v-html="_data[_include]"></td></tr></tbody>\
				</table>'
});